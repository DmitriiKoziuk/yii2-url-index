<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\services;

use yii\base\Model;
use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\ModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlCreateFormNotValidException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlUpdateFormNotValidException;

class UrlIndexService extends DBActionService implements UrlIndexServiceInterface
{
    use ModelValidatorTrait;

    private UrlRepositoryInterface $urlRepository;
    private UrlModuleRepositoryInterface $moduleRepository;

    public function __construct(
        UrlRepositoryInterface $urlRepository,
        UrlModuleRepositoryInterface $moduleRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->urlRepository = $urlRepository;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @param UrlCreateForm $urlCreateForm
     * @return UrlEntity
     * @throws ExternalComponentException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws UrlCreateFormNotValidException
     * @throws \Throwable
     */
    public function addUrl(UrlCreateForm $urlCreateForm): UrlEntity
    {
        if (! $urlCreateForm->validate()) {
            throw new UrlCreateFormNotValidException();
        }
        $existUrlEntity = $this->urlRepository->getByUrl($urlCreateForm->url);
        if (
            ! is_null($existUrlEntity) &&
            ! $existUrlEntity->isRedirect()
        ) {
            throw new UrlAlreadyHasBeenTakenException("Url '{$urlCreateForm->url}' already exist in index.");
        }
        $this->beginTransaction();
        try {
            if (
                ! is_null($existUrlEntity) &&
                $existUrlEntity->isRedirect()
            ) {
                $this->urlRepository->delete($existUrlEntity);
            }
            $moduleEntity = $this->moduleRepository->getModule(
                $urlCreateForm->module_name,
                $urlCreateForm->controller_name,
                $urlCreateForm->action_name
            );
            if (empty($moduleEntity)) {
                $moduleEntity = new ModuleEntity();
                $moduleEntity->module_name = $urlCreateForm->module_name;
                $moduleEntity->controller_name = $urlCreateForm->controller_name;
                $moduleEntity->action_name = $urlCreateForm->action_name;
                $this->moduleRepository->save($moduleEntity);
            }

            $urlEntity = new UrlEntity();
            $urlEntity->module_id = $moduleEntity->id;
            $urlEntity->entity_id = $urlCreateForm->entity_id;
            $urlEntity->url = $urlCreateForm->url;
            $this->urlRepository->save($urlEntity);
            $this->commitTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $urlEntity;
    }

    /**
     * @param UrlUpdateForm $urlUpdateForm
     * @return UrlUpdateForm
     * @throws ExternalComponentException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws UrlNotFoundException|UrlUpdateFormNotValidException
     */
    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm
    {
        if (! $urlUpdateForm->validate()) {
            throw new UrlUpdateFormNotValidException();
        }

        $updatedUrl = $this->urlRepository->getById($urlUpdateForm->id);
        if (is_null($updatedUrl)) {
            throw new UrlNotFoundException("Url with id '{$urlUpdateForm->id}' not found.");
        }

        $existUrl = $this->urlRepository->getByUrl($urlUpdateForm->url);
        if (
            ! is_null($existUrl) &&
            $updatedUrl->id != $existUrl->id &&
            ! $existUrl->isRedirect()
        ) {
            throw new UrlAlreadyHasBeenTakenException("Url '{$urlUpdateForm->url}' already exist in index.");
        }

        try {
            $this->beginTransaction();
            if (
                ! is_null($existUrl) &&
                $updatedUrl->id != $existUrl->id &&
                $existUrl->isRedirect()
            ) {
                $this->urlRepository->delete($existUrl);
            }
            $this->_updateUrl(
                $updatedUrl,
                $urlUpdateForm
            );
            $this->commitTransaction();
            return $urlUpdateForm;
        } catch (ExternalComponentException $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param string $url
     * @throws UrlNotFoundException
     * @throws ExternalComponentException
     */
    public function removeUrl(string $url): void
    {
        $urlEntity = $this->urlRepository->getByUrl($url);
        if (is_null($urlEntity)) {
            throw new UrlNotFoundException("Url '{$url}' not found.");
        }
        try {
            $this->beginTransaction();
            /** @var UrlEntity[] $redirects */
            $redirects = $this->urlRepository->getRedirects($urlEntity->id);
            foreach ($redirects as $redirect) {
                $this->urlRepository->delete($redirect);
            }
            $this->urlRepository->delete($urlEntity);
            $this->commitTransaction();
            return;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param string $url
     * @param UrlEntity $destinationUrlEntity
     * @throws ExternalComponentException
     */
    private function createRedirectUrl(string $url, UrlEntity $destinationUrlEntity)
    {
        $redirectModuleEntity = $this->moduleRepository->getModule(
            UrlIndexModule::getId(),
            'url',
            'redirect',
        );
        $redirectUrlEntity = new UrlEntity([
            'url' => $url,
            'redirect_to_url' => $destinationUrlEntity->id,
            'module_id' => $redirectModuleEntity->id,
            'entity_id' => 302,
        ]);
        $this->save($redirectUrlEntity);
    }

    /**
     * @param UrlEntity $urlEntity
     * @return UrlEntity
     * @throws ExternalComponentException
     */
    private function save(UrlEntity $urlEntity)
    {
        try {
            /** @var UrlEntity $urlEntity */
            $urlEntity = $this->urlRepository->save($urlEntity);
        } catch (EntityNotValidException| EntitySaveException $e) {
            $externalException = new ExternalComponentException();
            $externalException->addErrors([$e]);
            throw $externalException;
        }
        return $urlEntity;
    }

    /**
     * @param UrlEntity $updatedUrl
     * @param Model|UrlUpdateForm $updateEntityUrlForm
     * @throws ExternalComponentException
     */
    private function _updateUrl(
        UrlEntity $updatedUrl,
        Model $updateEntityUrlForm
    ): void {
        if (empty($updateEntityUrlForm->redirect_to_url)) {
            $urlModuleEntity = $this->findOrCreateUrlModuleEntity(
                $updateEntityUrlForm->module_name,
                $updateEntityUrlForm->controller_name,
                $updateEntityUrlForm->action_name
            );
        } else {
            $urlModuleEntity = $this->moduleRepository->getRedirectModule();
        }

        $oldUrl = $updatedUrl->url;
        $updatedUrl->setAttributes($updateEntityUrlForm->getAttributes([
            'entity_id',
            'url',
        ]));
        if (! empty($updateEntityUrlForm->redirect_to_url)) {
            $updatedUrl->redirect_to_url = $updateEntityUrlForm->redirect_to_url;
        }
        $updatedUrl->module_id = $urlModuleEntity->id;
        $isUrlChanged = $updatedUrl->isAttributeChanged('url');
        $updatedUrl = $this->save($updatedUrl);
        if ($isUrlChanged) {
            $this->createRedirectUrl($oldUrl, $updatedUrl);
        }
    }

    private function findOrCreateUrlModuleEntity(
        string $moduleName,
        string $controllerName,
        string $actionName
    ): ModuleEntity {
        $moduleEntity = $this->moduleRepository->getModule(
            $moduleName,
            $controllerName,
            $actionName
        );
        if (empty($moduleEntity)) {
            $moduleEntity = new ModuleEntity();
            $moduleEntity->module_name = $moduleName;
            $moduleEntity->controller_name = $controllerName;
            $moduleEntity->action_name = $actionName;
            $this->moduleRepository->save($moduleEntity);
        }
        return $moduleEntity;
    }
}
