<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

use yii\base\Model;

class UpdateEntityUrlForm extends Model
{
    public $url;

    public $module_name;

    public $controller_name;

    public $action_name;

    public $entity_id;

    public function rules()
    {
        return [
            [['url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'required'],
            [['url'], 'string', 'max' => 255],
            ['url', function ($attribute) {
                if (($firstChar = mb_substr($this->$attribute, 0, 1)) !== '/') {
                    $this->addError($attribute, 'Url must start from "/" character.');
                }
            }],
            ['url' , function ($attribute) {
                if (preg_match('/\S$/', $this->$attribute, $matches) === 0) {
                    $this->addError($attribute, 'Url cant end by space.');
                }
            }],
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