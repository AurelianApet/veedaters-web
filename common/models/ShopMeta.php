<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%shop_meta}}".
 *
 * @property integer $shop_meta_id
 * @property string $meta_key
 * @property integer $shop_id
 * @property string $meta_value
 * @property integer $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class ShopMeta extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_key', 'shop_id', 'meta_value'], 'required'],
            [['shop_id', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['meta_value'], 'string'],
            [['createddate', 'updateddate'], 'safe'],
            [['meta_key'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shop_meta_id' => 'Shop Meta ID',
            'meta_key' => 'Meta Key',
            'shop_id' => 'Shop ID',
            'meta_value' => 'Meta Value',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return ShopMetaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShopMetaQuery(get_called_class());
    }
}
