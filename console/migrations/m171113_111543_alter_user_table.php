<?php

use yii\db\Migration;

/**
 * Class m171113_111543_alter_user_table
 */
class m171113_111543_alter_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'social_media_type', $this->string(50)->after('status'));
        $this->addColumn('{{%user}}', 'social_id', $this->string(500)->after('status'));
        // $this->addColumn('{{%user}}', 'name', $this->string(50)->after('status'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171113_111543_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_111543_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
