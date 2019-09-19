<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

use yii\base\Model;

class UrlCreateForm extends Model
{
    public $url;

    public $redirect_to_url;

    public $module_name;

    public $controller_name;

    public $action_name;

    public $entity_id;

    public function rules()
    {
        return [
            [
                ['url', 'controller_name', 'action_name', 'entity_id'],
                'required'
            ],
            [
                ['url', 'redirect_to_url'],
                'string',
                'max' => 255
            ],
            [
                ['module_name', 'controller_name', 'action_name', 'entity_id'],
                'string',
                'max' => 45
            ],
            [
                ['module_name', 'redirect_to_url'],
                'default',
                'value' => null
            ],
        ];
    }
}