<?php

use yii\db\Migration;

/**
 * Class m180101_073218_rename_new_subscriptions
 */
class m180101_073218_rename_new_subscriptions extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         $this->renameTable('{{%subscriptions}}','{{%subscription}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180101_073218_rename_new_subscriptions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180101_073218_rename_new_subscriptions cannot be reverted.\n";

        return false;
    }
    */
}
