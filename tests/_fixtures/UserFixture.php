<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\tests\_fixtures;

use yii\test\ActiveFixture;
use common\models\User;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}
