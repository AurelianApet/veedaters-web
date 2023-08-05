<?php

use yii\db\Migration;

/**
 * Class m180101_073001_rename_subscriptions
 */
class m180101_073001_rename_subscriptions extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         //$this->dropTable('{{%subscription}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180101_073001_rename_subscriptions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180101_073001_rename_subscriptions cannot be reverted.\n";

        return false;
    }
    */
}
