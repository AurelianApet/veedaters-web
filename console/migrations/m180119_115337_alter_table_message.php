<?php

use yii\db\Migration;

/**
 * Class m180119_115337_alter_table_message
 */
class m180119_115337_alter_table_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%message}}', 'clear_for_sender', $this->integer(11)->defaultValue(0));
        $this->alterColumn('{{%message}}', 'clear_for_recipient', $this->integer(11)->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180119_115337_alter_table_message cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180119_115337_alter_table_message cannot be reverted.\n";

        return false;
    }
    */
}
