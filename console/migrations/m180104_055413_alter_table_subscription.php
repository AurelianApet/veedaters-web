<?php

use yii\db\Migration;

/**
 * Class m180104_055413_alter_table_subscription
 */
class m180104_055413_alter_table_subscription extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%subscription}}', 'customer_id', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180104_055413_alter_table_subscription cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180104_055413_alter_table_subscription cannot be reverted.\n";

        return false;
    }
    */
}
