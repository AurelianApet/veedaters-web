<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ShopMeta]].
 *
 * @see ShopMeta
 */
class ShopMetaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ShopMeta[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ShopMeta|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
