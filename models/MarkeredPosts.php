<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "markered_posts".
 *
 * @property string $id_mark_post
 * @property string $id_post
 * @property string $source
 * @property string $id_page
 * @property int $id_marker
 * @property int $id_user
 *
 * @property Markers $marker
 */
class MarkeredPosts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'markered_posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_post', 'source', 'id_page', 'id_marker', 'id_user'], 'required'],
            [['id_marker', 'id_user'], 'integer'],
            [['id_post', 'source', 'id_page'], 'string', 'max' => 255],
            [['id_marker'], 'exist', 'skipOnError' => true, 'targetClass' => Markers::className(), 'targetAttribute' => ['id_marker' => 'id_marker']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_mark_post' => 'Id Mark Post',
            'id_post' => 'Id Post',
            'source' => 'Source',
            'id_page' => 'Id Page',
            'id_marker' => 'Id Marker',
            'id_user' => 'Id User',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarker()
    {
        return $this->hasOne(Markers::className(), ['id_marker' => 'id_marker']);
    }

    public function getKil($posts){
        $kill=0;
        foreach($posts as $post){
            if($post['marker'] == 'розіграш'){$kill++;}
        }
        return $kill;
    }
}
