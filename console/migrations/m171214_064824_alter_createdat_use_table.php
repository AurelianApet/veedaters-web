<?php

use yii\db\Migration;

class m171214_064824_alter_createdat_use_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%user}}', 'created_at');
        $this->dropColumn('{{%user}}', 'updated_at');
    }

    public function safeDown()
    {
        echo "m171214_064824_alter_createdat_use_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171214_064824_alter_createdat_use_table cannot be reverted.\n";

        return false;
    }
    */
}
