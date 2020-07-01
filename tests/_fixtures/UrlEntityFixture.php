<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_fixtures;

use yii\test\ActiveFixture;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlEntityFixture extends ActiveFixture
{
    public $modelClass = UrlEntity::class;
}
