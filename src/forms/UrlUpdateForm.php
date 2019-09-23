<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

class UrlUpdateForm extends UrlCreateForm
{
    public $id;

    public $created_at;

    public $updated_at;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['id', 'url', 'controller_name', 'action_name', 'entity_id'],
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
            [
                ['id', 'created_at', 'updated_at'],
                'integer'
            ],
        ];
    }
}