<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_fixtures;

use yii\test\ActiveFixture;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlModuleEntity;

class ModuleEntityFixture extends ActiveFixture
{
    public $modelClass = UrlModuleEntity::class;
}
