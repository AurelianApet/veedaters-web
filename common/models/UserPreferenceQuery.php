<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Notification]].
 *
 * @see Notification
 */
class UserPreferenceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Notification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Notification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
