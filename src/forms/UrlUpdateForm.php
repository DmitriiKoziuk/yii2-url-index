<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\forms;

class UrlUpdateForm extends UrlCreateForm
{
    public $id;

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
            ['id'], 'integer'
        ];
        return $rules;
    }
}
