<?php

use yii\db\Migration;

/**
 * Class m180117_053636_rename_message_column
 */
class m180117_053636_rename_message_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn('{{%message}}', 'clear_for', 'clear_for_recipient');
        $this->addColumn('{{%message}}', 'clear_for_sender', $this->integer(11)->after('delete_for'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180117_053636_rename_message_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180117_053636_rename_message_column cannot be reverted.\n";

        return false;
    }
    */
}
