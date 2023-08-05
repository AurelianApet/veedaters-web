<?php

use yii\db\Migration;

/**
 * Class m171208_073400_alter_table_blocklist
 */
class m171208_073400_alter_table_blocklist extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // $this->renameColumn('{{%blocklist}}', 'created_at', 'createddate');
        // $this->renameColumn('{{%blocklist}}', 'updated_at', 'updateddate');
        // $this->renameColumn('{{%blocklist}}', 'created_by', 'createdby');
        // $this->renameColumn('{{%blocklist}}', 'updated_by', 'updatedby');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171208_073400_alter_table_blocklist cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171208_073400_alter_table_blocklist cannot be reverted.\n";

        return false;
    }
    */
}
