<?php

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use yii\db\ActiveQuery;
use DmitriiKoziuk\yii2Base\interfaces\ActiveRecordRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;

interface UrlRepositoryInterface extends ActiveRecordRepositoryInterface
{
    public function getById(int $id): ?UrlEntity;

    public function getByUrl(string $url): ?UrlEntity;

    public function getRedirects(int $urlId): array;

    public function urlSearchQueryBuilder(UrlSearchForm $form): ActiveQuery;
}