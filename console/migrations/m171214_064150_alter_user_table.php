<?php

use yii\db\Migration;

class m171214_064150_alter_user_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%user}}', 'auth_key');
        $this->addColumn('{{%user}}', 'auth_key', $this->string()->null());

    }

    public function safeDown()
    {
        echo "m171214_064150_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171214_064150_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
