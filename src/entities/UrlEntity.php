<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\forms\UpdateEntityUrlForm;

/**
 * This is the model class for table "{{%dk_url_index_urls}}".
 *
 * @property int    $id
 * @property string $url
 * @property int    $redirect_to_url
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
                ['url'],
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
                ['redirect_to_url', 'module_name'],
                'default',
                'value' => null
            ],
            [
                ['redirect_to_url', 'created_at', 'updated_at'],
                'integer'
            ],
            [
                ['redirect_to_url'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UrlEntity::class,
                'targetAttribute' => ['redirect_to_url' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t(UrlIndexModule::TRANSLATE, 'Url'),
            'redirect_to_url' => Yii::t(UrlIndexModule::TRANSLATE, 'Redirect To Url'),
            'module_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Module Name'),
            'controller_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Controller Name'),
            'action_name' => Yii::t(UrlIndexModule::TRANSLATE, 'Action Name'),
            'entity_id' => Yii::t(UrlIndexModule::TRANSLATE, 'Entity ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRedirectUrl()
    {
        return $this->hasOne(UrlEntity::class, ['id' => 'redirect_to_url']);
    }

    public function isRedirect(): bool
    {
        return ! is_null($this->redirect_to_url);
    }

    public function isOwner(UpdateEntityUrlForm $updateEntityUrlForm): bool
    {
        $entityAttributes = $this->getAttributes([
            'module_name',
            'controller_name',
            'action_name',
            'entity_id',
        ]);
        $formAttributes = $updateEntityUrlForm->getAttributes([
            'module_name',
            'controller_name',
            'action_name',
            'entity_id',
        ]);
        if (empty(array_diff($entityAttributes, $formAttributes))) {
            return true;
        }
        return false;
    }
}
