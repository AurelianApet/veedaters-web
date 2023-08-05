<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[MessageRecipient]].
 *
 * @see MessageRecipient
 */
class MessageRecipientQuery extends \common\models\ModelBase
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MessageRecipient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MessageRecipient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
