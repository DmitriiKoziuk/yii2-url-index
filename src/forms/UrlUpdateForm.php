<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

use yii\helpers\ArrayHelper;

class UrlUpdateForm extends UrlCreateForm
{
    public $id;

    public $created_at;

    public $updated_at;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['id', 'created_at', 'updated_at'], 'integer'
            ]
        );
    }
}