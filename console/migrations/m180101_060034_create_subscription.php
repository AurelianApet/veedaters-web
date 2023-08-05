<?php

use yii\db\Migration;

/**
 * Class m180101_060034_create_subscription
 */
class m180101_060034_create_subscription extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%subscriptions}}', [
            'id' => $this->primaryKey()->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'charge_id' =>$this->string(),  
            'transaction_id' => $this->string(), 
            'plan' => $this->string(), 
            'amount' => $this->integer(11),
            'months' => $this->integer(11)->defaultValue(3),
            'expires_on' => $this->datetime(),
            'createddate' => $this->datetime()->defaultValue('CURRENT_TIMESTAMP'),
            'updateddate' => $this->datetime()->defaultValue('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180101_060034_create_subscription cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180101_060034_create_subscription cannot be reverted.\n";

        return false;
    }
    */
}
