<?php

use yii\db\Migration;

/**
 * Class m180103_113832_alter_message_table
 */
class m180103_113832_alter_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         $this->addColumn('{{%message}}', 'delete_for', $this->integer(11)->after('createdby')->null());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180103_113832_alter_message_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180103_113832_alter_message_table cannot be reverted.\n";

        return false;
    }
    */
}
