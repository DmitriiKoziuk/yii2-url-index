<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

class UrlSearchForm extends UrlUpdateForm
{
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['url', 'redirect_to_url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'safe'],
        ];
    }
}