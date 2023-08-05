<?php

use yii\db\Migration;

/**
 * Class m171113_102640_alter_user_table
 */
class m171113_102640_alter_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'verificationcode', $this->string(500)->after('status'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171113_102640_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_102640_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
