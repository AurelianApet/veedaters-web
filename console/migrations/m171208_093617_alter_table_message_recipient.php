<?php

use yii\db\Migration;

/**
 * Class m171208_093617_alter_table_message_recipient
 */
class m171208_093617_alter_table_message_recipient extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%message_recipient}}', 'recipient_id', $this->integer(11)->after('message_id')->null());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171208_093617_alter_table_message_recipient cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171208_093617_alter_table_message_recipient cannot be reverted.\n";

        return false;
    }
    */
}
