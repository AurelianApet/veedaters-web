<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[UserMeta]].
 *
 * @see UserMeta
 */
class UserMetaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserMeta[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserMeta|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
