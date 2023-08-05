<?php

use yii\db\Migration;

/**
 * Class m180124_125807_alter_user_table
 */
class m180124_125807_alter_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user}}', 'address', $this->string(200)->null());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180124_125807_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180124_125807_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
