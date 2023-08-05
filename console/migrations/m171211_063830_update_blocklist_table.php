<?php

use yii\db\Migration;

class m171211_063830_update_blocklist_table extends Migration
{
    public function safeUp()
    {
        
        // $this->addColumn('{{%blocklist}}', 'createdby', $this->integer());
        // $this->addColumn('{{%blocklist}}', 'updatedby', $this->integer());
        // $this->addColumn('{{%blocklist}}', 'createddate', $this->dateTime());
        // $this->addColumn('{{%blocklist}}', 'updateddate', $this->dateTime());
        // $this->dropColumn('{{%blocklist}}', 'created_at');
        // $this->dropColumn('{{%blocklist}}', 'updated_at');
        // $this->dropColumn('{{%blocklist}}', 'created_by');
        // $this->dropColumn('{{%blocklist}}', 'updated_by');
    }

    public function safeDown()
    {
        echo "m171211_063830_update_blocklist_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171211_063830_update_blocklist_table cannot be reverted.\n";

        return false;
    }
    */
}
