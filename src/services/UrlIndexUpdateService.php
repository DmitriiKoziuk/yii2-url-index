<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\services;

use yii\base\Model;
use yii\db\Connection;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlUpdateFormNotValidException;

class UrlIndexUpdateService extends DBActionService
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
     * @param UrlEntity $urlEntity
     * @param string $newUrl
     * @throws ExternalComponentException
     * @throws UrlAlreadyHasBeenTakenException
     */
    public function updateUrlByEntity(UrlEntity $urlEntity, string $newUrl): void
    {
        $existUrl = $this->urlRepository->getByUrl($newUrl);
        if ($this->isCanNotUpdateExistUrl($urlEntity, $existUrl)) {
            throw new UrlAlreadyHasBeenTakenException("Url '{$newUrl}' already exist in index.");
        }
        try {
            $this->beginTransaction();
            if (
                ! is_null($existUrl) &&
                $urlEntity->id != $existUrl->id &&
                $existUrl->isRedirect()
            ) {
                $this->urlRepository->delete($existUrl);
            }
            $oldUrl = $urlEntity->url;
            $urlEntity->url = $newUrl;
            $this->save($urlEntity);
            $this->createRedirectUrl($oldUrl, $urlEntity);
            $this->commitTransaction();
        } catch (ExternalComponentException $e) {
            $this->rollbackTransaction();
            throw $e;
        }
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
        if ($this->isCanNotUpdateExistUrl($updatedUrl, $existUrl)) {
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

    private function findOrCreateUrlModuleEntity(
        string $moduleName,
        string $controllerName,
        string $actionName
    ): UrlModuleEntity {
        $moduleEntity = $this->moduleRepository->getModule(
            $moduleName,
            $controllerName,
            $actionName
        );
        if (empty($moduleEntity)) {
            $moduleEntity = new UrlModuleEntity();
            $moduleEntity->module_name = $moduleName;
            $moduleEntity->controller_name = $controllerName;
            $moduleEntity->action_name = $actionName;
            $this->moduleRepository->save($moduleEntity);
        }
        return $moduleEntity;
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
        } catch (EntityNotValidException | EntitySaveException $e) {
            $externalException = new ExternalComponentException();
            $externalException->addErrors([$e]);
            throw $externalException;
        }
        return $urlEntity;
    }

    public function isCanNotUpdateExistUrl(UrlEntity $updatedUrl, UrlEntity $existUrl = null): bool
    {
        return !is_null($existUrl) && $updatedUrl->id != $existUrl->id && !$existUrl->isRedirect();
    }
}
