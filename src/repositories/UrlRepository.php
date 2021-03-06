<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\repositories;

use yii\db\ActiveQuery;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;
use DmitriiKoziuk\yii2UrlIndex\interfaces\UrlRepositoryInterface;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

class UrlRepository extends AbstractActiveRecordRepository implements UrlRepositoryInterface
{
    public function getById(int $id): ?UrlEntity
    {
        /** @var UrlEntity|null $entity */
        $entity = UrlEntity::find()->where(['id' => $id])->one();
        return $entity;
    }

    public function getByUrl(string $url): ?UrlEntity
    {
        /** @var UrlEntity|null $entity */
        $entity = UrlEntity::find()->where(['url' => $url])->one();
        return $entity;
    }

    public function getRedirects(int $urlId): array
    {
        $entities = UrlEntity::find()->where(['redirect_to_url' => $urlId])->all();
        return $entities;
    }

    public function getEntityUrl(
        string $moduleName,
        string $controllerName,
        string $actionName,
        string $entityId
    ): ?UrlEntity {
        /** @var UrlEntity|null $entity */
        $entity = UrlEntity::find()
            ->where([
                'module_name' => $moduleName,
                'controller_name' => $controllerName,
                'action_name' => $actionName,
                'entity_id' => $entityId,
            ])
            ->one();
        return $entity;
    }

    /**
     * @param UrlSearchForm $form
     * @return ActiveQuery
     */
    public function urlSearchQueryBuilder(UrlSearchForm $form): ActiveQuery
    {
        $query = UrlEntity::find();
        $query->andFilterWhere([
            'id' => $form->id,
        ])->andFilterWhere(['like', 'url', $form->url])
        ->andFilterWhere(['like', 'redirect_to_url', $form->redirect_to_url])
        ->andFilterWhere(['like', 'module_name', $form->module_name])
        ->andFilterWhere(['like', 'controller_name', $form->controller_name])
        ->andFilterWhere(['like', 'action_name', $form->action_name])
        ->andFilterWhere(['like', 'entity_id', $form->entity_id]);
        return $query;
    }

    public function isUrlExist(string $url): bool
    {
        $urlEntity = $this->getByUrl($url);
        return ! empty($urlEntity);
    }
}
