<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_stubs;

use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use yii\db\ActiveRecord;

class UrlRepositoryStub implements UrlRepositoryInterface
{
    private $id;

    private $created_at;

    private $updated_at;

    public function __construct(int $id, string $created_at, string $updated_at)
    {
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getById(int $id): ?UrlEntity
    {
        return null;
    }

    public function getByUrl(string $url): ?UrlEntity
    {
        return null;
    }

    public function save(ActiveRecord $activeRecord): ActiveRecord
    {
        $activeRecord->id = $this->id;
        $activeRecord->created_at = $this->created_at;
        $activeRecord->updated_at = $this->updated_at;
        return $activeRecord;
    }

    public function delete(ActiveRecord $activeRecord): void
    {
    }
}