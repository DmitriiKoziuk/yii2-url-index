<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\services;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\traits\ModelValidator;
use DmitriiKoziuk\yii2Base\exceptions\DataNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\InvalidFormException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlIndexServiceInterface;
use DmitriiKoziuk\yii2UrlIndex\exceptions\UrlAlreadyExistException;

class UrlIndexService extends DBActionService implements UrlIndexServiceInterface
{
    use ModelValidator;

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
     * @throws DataNotValidException|InvalidFormException
     * @throws ExternalComponentException
     * @throws UrlAlreadyExistException
     * @return UrlUpdateForm
     */
    public function addUrl(UrlCreateForm $urlCreateForm): UrlUpdateForm
    {
        $this->validateModels(
            [$urlCreateForm],
            new InvalidFormException('UrlCreateForm not valid.')
        );
        if ($this->isUrlExist($urlCreateForm->url)) {
            throw new UrlAlreadyExistException("Url '{$urlCreateForm->url}' already exist in index.");
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
     * @throws EntityNotFoundException
     * @throws ExternalComponentException
     */
    public function updateUrl(UrlUpdateForm $urlUpdateForm): UrlUpdateForm
    {
        $this->validateModels(
            [$urlUpdateForm],
            new InvalidFormException('UrlUpdateForm not valid.')
        );

        $urlEntity = $this->urlRepository->getById($urlUpdateForm->id);
        if (empty($urlEntity)) {
            throw new EntityNotFoundException("UrlEntity with id '{$urlUpdateForm->id}' not found.");
        }
        $urlEntity->setAttributes($urlUpdateForm->getAttributes());
        $urlEntity = $this->save($urlEntity);
        $urlForm = new UrlUpdateForm($urlEntity->getAttributes());

        return $urlForm;
    }

    public function removeUrl(string $url): void
    {
        throw new \Exception('Method not implement.');
    }

    public function getUrlById(int $id): ?UrlUpdateForm
    {
        $urlEntity = $this->urlRepository->getById($id);
        if (empty($urlEntity)) {
            return null;
        }
        return new UrlUpdateForm($urlEntity->getAttributes());
    }

    public function isUrlExist(string $url): bool
    {
        return !is_null($this->urlRepository->getByUrl($url));
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