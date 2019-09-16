<?php
declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\entities;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UrlIndexEntitySearch represents the model behind the search form of `DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity`.
 */
class UrlEntitySearch extends UrlEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['url', 'redirect_to_url', 'module_name', 'controller_name', 'action_name', 'entity_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UrlEntity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'redirect_to_url', $this->redirect_to_url])
            ->andFilterWhere(['like', 'module_name', $this->module_name])
            ->andFilterWhere(['like', 'controller_name', $this->controller_name])
            ->andFilterWhere(['like', 'action_name', $this->action_name])
            ->andFilterWhere(['like', 'entity_id', $this->entity_id]);

        return $dataProvider;
    }
}
