<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\services;

use yii\db\Connection;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyHasBeenTakenException;

class UrlIndexService extends DBActionService implements UrlIndexServiceInterface
{
    use ModelValidatorTrait;

    /**
     * @var UrlRepositoryInterface
     */
    private $urlRepository;

    public function __construct(
        UrlRepositoryInterface $urlRepository,
        Connection $db = null
    ) {
        $this->urlRepository = $urlRepository;
        parent::__construct($db);
    }

    /**
     * @param UrlCreateForm $urlCreateForm
     * @return UrlUpdateForm
     *@throws ExternalComponentException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws DataNotValidException|InvalidFormException
     */
    public function addUrl(UrlCreateForm $urlCreateForm): UrlUpdateForm
    {
        $this->validateModels(
            [$urlCreateForm],
            new InvalidFormException('UrlCreateForm not valid.')
        );
        if ($this->isUrlExist($urlCreateForm->url)) {
            throw new UrlAlreadyHasBeenTakenException("Url '{$urlCreateForm->url}' already exist in index.");
        }
        $urlEntity = new UrlEntity();
        $urlEntity->setAttributes($urlCreateForm->getAttributes());
        $urlEntity = $this->save($urlEntity);
        $urlForm = new UrlUpdateForm($urlEntity->getAttributes());
        return $urlForm;
    }

    /**
     * @param UrlUpdateForm $urlUpdateForm
     * @return UrlUpdateForm
     * @throws DataNotValidException|InvalidFormException
     * @throws ExternalComponentException
     * @throws UrlAlreadyHasBeenTakenException
     * @throws UrlNotFoundException
     */
    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm
    {
        $this->validateModels(
            [$urlUpdateForm],
            new InvalidFormException('UrlUpdateForm not valid.')
        );

        $updatedUrl = $this->urlRepository->getById($urlUpdateForm->id);
        if (is_null($updatedUrl)) {
            throw new UrlNotFoundException("Url with id '{$urlUpdateForm->id}' not found.");
        }

        $existUrl = $this->urlRepository->getByUrl($urlUpdateForm->url);
        if (! is_null($existUrl) && ! $existUrl->isRedirect()) {
            throw new UrlAlreadyHasBeenTakenException("Url '{$urlUpdateForm->url}' already exist in index.");
        }

        try {
            $this->beginTransaction();
            if (! is_null($existUrl)) {
                $this->urlRepository->delete($existUrl);
            }
            $oldUrl = $updatedUrl->url;
            $updatedAttributes = $urlUpdateForm->getAttributes();
            unset($updatedAttributes['id'], $updatedAttributes['created_at'], $updatedAttributes['updated_at']);
            $updatedUrl->setAttributes($updatedAttributes);
            $isUrlChanged = $updatedUrl->isAttributeChanged('url');
            $updatedUrl = $this->save($updatedUrl);
            $urlForm = new UrlUpdateForm($updatedUrl->getAttributes());
            if ($isUrlChanged) {
                $this->createRedirectUrl($oldUrl, $updatedUrl->id);
            }
            $this->commitTransaction();
            return $urlForm;
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

    public function getUrlById(int $id): ?UrlUpdateForm
    {
        $urlEntity = $this->urlRepository->getById($id);
        if (empty($urlEntity)) {
            return null;
        }
        return new UrlUpdateForm($urlEntity->getAttributes());
    }

    public function getUrlByUrl(string $url): ?UrlUpdateForm
    {
        $urlEntity = $this->urlRepository->getByUrl($url);
        if (is_null($urlEntity)) {
            return null;
        }
        return new UrlUpdateForm($urlEntity->getAttributes());
    }

    public function isUrlExist(string $url): bool
    {
        return !is_null($this->urlRepository->getByUrl($url));
    }

    public function search(UrlSearchForm $urlSearchForm): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->urlRepository->urlSearchQueryBuilder($urlSearchForm),
        ]);
    }

    /**
     * @param string $url
     * @param int $redirectToUrlId
     * @throws ExternalComponentException
     */
    private function createRedirectUrl(string $url, int $redirectToUrlId)
    {
        $redirectUrlEntity = new UrlEntity([
            'url' => $url,
            'redirect_to_url' => $redirectToUrlId,
            'module_name' => UrlIndexModule::getId(),
            'controller_name' => 'url',
            'action_name' => 'redirect',
            'entity_id' => '302',
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
}