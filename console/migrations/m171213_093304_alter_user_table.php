<?php

use yii\db\Migration;

class m171213_093304_alter_user_table extends Migration
{
    public function safeUp()
    {
        // $this->addColumn('{{%user}}', 'latitude', $this->double()->null());
        // $this->addColumn('{{%user}}', 'longitude', $this->double()->null());
    }

    public function safeDown()
    {
        echo "m171213_093304_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171213_093304_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
