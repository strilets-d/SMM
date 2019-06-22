<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts_for_comparison".
 *
 * @property string $id
 * @property string $id_post
 * @property string $source
 * @property string $id_user
 * @property string $comparator
 *
 * @property User $user
 * @property User $comparator0
 */
class PostsForComparison extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_for_comparison';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_post', 'source', 'id_user', 'comparator'], 'required'],
            [['id_user', 'comparator'], 'integer'],
            [['id_post', 'source'], 'string', 'max' => 255],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            [['comparator'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['comparator' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_post' => 'Id Post',
            'source' => 'Source',
            'id_user' => 'Id User',
            'comparator' => 'Comparator',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparator0()
    {
        return $this->hasOne(User::className(), ['id' => 'comparator']);
    }
}
