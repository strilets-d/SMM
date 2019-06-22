<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MarkeredPosts;

/**
 * MarkeredPostSearch represents the model behind the search form of `app\models\MarkeredPosts`.
 */
class MarkeredPostSearch extends MarkeredPosts
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_mark_post', 'id_marker', 'id_user'], 'integer'],
            [['id_post', 'source', 'id_page'], 'safe'],
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
        $query = MarkeredPosts::find();

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
            'id_mark_post' => $this->id_mark_post,
            'id_marker' => $this->id_marker,
            'id_user' => $this->id_user,
        ]);

        $query->andFilterWhere(['like', 'id_post', $this->id_post])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'id_page', $this->id_page]);

        return $dataProvider;
    }
}
