<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

use yii\base\Model;

class RemoveEntityForm extends Model
{
    public $module_name;

    public $controller_name;

    public $action_name;

    public $entity_id;

    public function rules()
    {
        return [
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['module_name', 'controller_name', 'action_name', 'entity_id'], 'string', 'max' => 45],
            [
                ['module_name', 'controller_name', 'action_name', 'entity_id'],
                function ($attribute, $params, $validators) {
                    if (0 === preg_match('/^[a-zA-Z0-9_-]*$/', $this->$attribute, $matches)) {
                        $this->addError($attribute, 'Attribute must contain only: characters, digits, underscores and hyphen.');
                    }
                }
            ],
        ];
    }
}