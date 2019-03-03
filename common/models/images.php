<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "images".
 *
 * @property string $image_id 图片ID
 * @property string $storage 存储引擎
 * @property string $image_name 图片名称
 * @property string $ident
 * @property string $url 网址
 * @property string $l_ident 大图唯一标识
 * @property string $l_url 大图URL地址
 * @property string $m_ident 中图唯一标识
 * @property string $m_url 中图URL地址
 * @property string $s_ident 小图唯一标识
 * @property string $s_url 小图URL地址
 * @property string $width 宽度
 * @property string $height 高度
 * @property string $watermark 有水印
 * @property string $last_modified 更新时间
 */
class images extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'ident', 'url'], 'required'],
            [['width', 'height', 'last_modified'], 'integer'],
            [['watermark'], 'string'],
            [['image_id'], 'string', 'max' => 32],
            [['storage', 'image_name'], 'string', 'max' => 50],
            [['ident', 'url', 'l_ident', 'l_url', 'm_ident', 'm_url', 's_ident', 's_url'], 'string', 'max' => 200],
            [['image_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'image_id' => '图片ID',
            'storage' => '存储引擎',
            'image_name' => '图片名称',
            'ident' => 'Ident',
            'url' => '网址',
            'l_ident' => '大图唯一标识',
            'l_url' => '大图URL地址',
            'm_ident' => '中图唯一标识',
            'm_url' => '中图URL地址',
            's_ident' => '小图唯一标识',
            's_url' => '小图URL地址',
            'width' => '宽度',
            'height' => '高度',
            'watermark' => '有水印',
            'last_modified' => '更新时间',
        ];
    }

    /**
     * 获取单挑数据
     * @param $id
     * @return array()
     */
    static public function getOne($id)
    {
        return self::find()->where('image_id=:image_id', [':image_id' => $id])->asArray()->one();
    }
}
