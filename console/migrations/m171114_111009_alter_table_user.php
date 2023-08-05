<?php

use yii\db\Migration;

/**
 * Class m171114_111009_alter_table_user
 */
class m171114_111009_alter_table_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'name', $this->string(50)->after('status'));
        $this->addColumn('{{%user}}', 'address', $this->string(50)->after('status'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171114_111009_alter_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171114_111009_alter_table_user cannot be reverted.\n";

        return false;
    }
    */
}
