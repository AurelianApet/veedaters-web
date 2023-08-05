<?php

use yii\db\Migration;

/**
 * Class m171113_130529_create_table_photomap
 */
class m171113_130529_create_table_photomap extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%photos_map}}', [
            'photos_map_id' => $this->primaryKey()->notNull(),
            'photos_id' => $this->integer(11)->notNull(),
            'item_id' => $this->integer(11)->notNull(),
            'relationship' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->notNull(),
            'createdby' => $this->integer(11)->notNull(),
            'createddate' => $this->datetime()->notNull(),
            'updatedby' => $this->integer(11)->notNull(),
            'updateddate' => $this->datetime()->notNull(),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171113_130529_create_table_photomap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_130529_create_table_photomap cannot be reverted.\n";

        return false;
    }
    */
}
