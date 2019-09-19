<?php

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use DmitriiKoziuk\yii2Base\interfaces\ActiveRecordRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

interface UrlRepositoryInterface extends ActiveRecordRepositoryInterface
{
    public function getById(int $id): ?UrlEntity;

    public function getByUrl(string $url): ?UrlEntity;
}