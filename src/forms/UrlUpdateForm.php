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
        $rules = parent::rules();
        $rules[] = [
            ['id'], 'required'
        ];
        $rules[] = [
            ['id', 'created_at', 'updated_at'], 'integer'
        ];
        return $rules;
    }
}