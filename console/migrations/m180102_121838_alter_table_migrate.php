<?php

use yii\db\Migration;

/**
 * Class m180102_121838_alter_table_migrate
 */
class m180102_121838_alter_table_migrate extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%message}}', 'clear_for', $this->integer(11)->after('createdby')->null());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180102_121838_alter_table_migrate cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180102_121838_alter_table_migrate cannot be reverted.\n";

        return false;
    }
    */
}
