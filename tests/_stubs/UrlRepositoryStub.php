<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_stubs;

use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use yii\db\ActiveRecord;

class UrlRepositoryStub implements UrlRepositoryInterface
{
    public function getById(int $id): ?UrlEntity
    {
        return null;
    }

    public function getByUrl(string $url): ?UrlEntity
    {
        return null;
    }

    public function save(ActiveRecord $activeRecord): void
    {
    }

    public function delete(ActiveRecord $activeRecord): void
    {
    }
}