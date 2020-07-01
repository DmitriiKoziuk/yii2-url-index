<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

/**
 * This is the model class for table "{{%dk_url_index_modules}}".
 *
 * @property int $id
 * @property string $module_name
 * @property string $controller_name
 * @property string $action_name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property UrlEntity[] $urls
 */
class ModuleEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%dk_url_index_modules}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['controller_name', 'action_name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['module_name', 'controller_name', 'action_name'], 'string', 'max' => 45],
            [
                [
                    'module_name',
                    'controller_name',
                    'action_name'
                ],
                'unique',
                'targetAttribute' => ['module_name', 'controller_name', 'action_name']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'module_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Module Name'),
            'controller_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Controller Name'),
            'action_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Action Name'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getUrls(): ActiveQuery
    {
        return $this->hasMany(UrlEntity::class, ['module_id' => 'id']);
    }
}
