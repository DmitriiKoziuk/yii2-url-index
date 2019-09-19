<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlRepository extends AbstractActiveRecordRepository implements UrlRepositoryInterface
{
    public function getById(int $id): ?UrlEntity
    {
        /** @var UrlEntity|null $entity */
        $entity = UrlEntity::find()->where(['id' => $id])->one();
        return $entity;
    }

    public function getByUrl(string $url): ?UrlEntity
    {
        /** @var UrlEntity|null $entity */
        $entity = UrlEntity::find()->where(['url' => $url])->one();
        return $entity;
    }
}