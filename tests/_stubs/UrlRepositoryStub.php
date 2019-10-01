<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_stubs;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;

class UrlRepositoryStub implements UrlRepositoryInterface
{
    private $data;

    public function __construct(array $data = null)
    {
        $this->data = empty($data) ? null : new UrlUpdateForm($data);
    }

    public function getById(int $id): ?UrlEntity
    {
        return empty($this->data) ? null : new UrlEntity($this->data->getAttributes());
    }

    public function getByUrl(string $url): ?UrlEntity
    {
        return empty($this->data) ? null : new UrlEntity($this->data->getAttributes());
    }

    public function save(ActiveRecord $activeRecord): ActiveRecord
    {
        return new UrlEntity($this->data->getAttributes());
    }

    public function delete(ActiveRecord $activeRecord): void
    {
    }

    public function urlSearchQueryBuilder(UrlSearchForm $form): ActiveQuery
    {
        return UrlEntity::find();
    }
}