<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\forms\UpdateEntityUrlForm;

/**
 * This is the model class for table "{{%dk_url_index_urls}}".
 *
 * @property int $id
 * @property int $module_id
 * @property int $entity_id
 * @property string $url
 * @property int|null $redirect_to_url
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ModuleEntity $moduleEntity
 * @property UrlEntity $redirectToUrl
 * @property UrlEntity[] $urlEntities
 */
class UrlEntity extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%dk_url_index_urls}}';
    }

    public function rules(): array
    {
        return [
            [['module_id', 'entity_id', 'url'], 'required'],
            [['module_id', 'redirect_to_url'], 'integer'],
            [['entity_id'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:m:s'],
            [['url'], 'string', 'max' => 255],
            [['url'], 'unique'],
            [
                ['module_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ModuleEntity::class,
                'targetAttribute' => ['module_id' => 'id']
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

    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t(UrlIndexModule::TRANSLATE, 'ID'),
            'module_id' => Yii::t(UrlIndexModule::TRANSLATE, 'Module ID'),
            'entity_id' => Yii::t(UrlIndexModule::TRANSLATE, 'Entity ID'),
            'url' => Yii::t(UrlIndexModule::TRANSLATE, 'Url'),
            'redirect_to_url' => Yii::t(UrlIndexModule::TRANSLATE, 'Redirect To Url'),
            'created_at' => Yii::t(UrlIndexModule::TRANSLATE, 'Created At'),
            'updated_at' => Yii::t(UrlIndexModule::TRANSLATE, 'Updated At'),
        ];
    }

    public function getRedirectUrl(): ActiveQuery
    {
        return $this->hasOne(UrlEntity::class, ['id' => 'redirect_to_url']);
    }

    public function getModuleEntity(): ActiveQuery
    {
        return $this->hasOne(ModuleEntity::class, ['id' => 'module_id']);
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
