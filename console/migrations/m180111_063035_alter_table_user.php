<?php

use yii\db\Migration;

/**
 * Class m180111_063035_alter_table_user
 */
class m180111_063035_alter_table_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_active', $this->integer(1)->after('subscription')->null()->defaultValue(1));
        $this->addColumn('{{%user}}', 'user_type', $this->integer(1)->after('is_active')->null()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180111_063035_alter_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180111_063035_alter_table_user cannot be reverted.\n";

        return false;
    }
    */
}
