<?php

use yii\db\Migration;

/**
 * Class m180103_112845_alter_subscription_table
 */
class m180103_112845_alter_subscription_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%subscription}}', 'amount', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180103_112845_alter_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180103_112845_alter_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
