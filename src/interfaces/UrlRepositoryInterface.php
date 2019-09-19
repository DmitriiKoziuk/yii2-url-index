<?php

namespace DmitriiKoziuk\yii2UrlIndex\interfaces;

use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

interface UrlRepositoryInterface
{
    public function getById(int $id): ?UrlEntity;

    public function getByUrl(string $url): ?UrlEntity;
}