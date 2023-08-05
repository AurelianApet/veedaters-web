<?php

use yii\db\Migration;

/**
 * Class m180108_115104_alter_table_preferance
 */
class m180108_115104_alter_table_preferance extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_preferences}}', 'createdby', $this->integer(11));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180108_115104_alter_table_preferance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180108_115104_alter_table_preferance cannot be reverted.\n";

        return false;
    }
    */
}
