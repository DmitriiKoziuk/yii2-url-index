<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\services;

use Throwable;
use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlModuleRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\forms\UrlCreateFormNotValidException;

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
     * @throws Throwable
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
                $moduleEntity = new UrlModuleEntity();
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
        } catch (Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
        return $urlEntity;
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
}
