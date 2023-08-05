<?php

use yii\db\Migration;

/**
 * Class m180111_055630_create_table_role_map
 */
class m180111_055630_create_table_role_map extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_role_map}}', [
            'user_role_map_id' => $this->primaryKey()->notNull(),
            'user_role_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
            'createdby' => $this->integer(11)->notNull(),
            'createddate' => $this->datetime()->notNull(),
            'updatedby' => $this->integer(11),
            'updateddate' => $this->datetime(),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180111_055630_create_table_role_map cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180111_055630_create_table_role_map cannot be reverted.\n";

        return false;
    }
    */
}
