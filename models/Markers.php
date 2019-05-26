<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "markers".
 *
 * @property int $id_marker
 * @property string $name_marker
 * @property string $description
 *
 * @property MarkeredPosts[] $markeredPosts
 */
class Markers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'markers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_marker', 'description'], 'required'],
            [['name_marker', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_marker' => 'Id Marker',
            'name_marker' => 'Name Marker',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarkeredPosts()
    {
        return $this->hasMany(MarkeredPosts::className(), ['id_marker' => 'id_marker']);
    }
}
