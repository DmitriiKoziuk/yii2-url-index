<?php

namespace DmitriiKoziuk\yii2UrlIndex\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

/**
 * This is the model class for table "{{%dk_url_index_urls}}".
 *
 * @property int    $id
 * @property string $url
 * @property string $redirect_to_url
 * @property string $module_name
 * @property string $controller_name
 * @property string $action_name
 * @property string $entity_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class UrlEntity extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_url_index_urls}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
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
                ['url'],
                'unique'
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
                ['created_at', 'updated_at'],
                'integer'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t(BaseModule::TRANSLATE, 'ID'),
            'url' => Yii::t(UrlIndexModule::TRANSLATE, 'Url'),
            'redirect_to_url' => Yii::t(UrlIndexModule::TRANSLATE, 'Redirect To Url'),
            'module_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Module Name'),
            'controller_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Controller Name'),
            'action_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Action Name'),
            'entity_id' => Yii::t(UrlIndexModule::TRANSLATE, 'Entity ID'),
            'created_at' => Yii::t(BaseModule::TRANSLATE, 'Created At'),
            'updated_at' => Yii::t(BaseModule::TRANSLATE, 'Updated At'),
        ];
    }
}