<?php

use yii\db\Migration;

/**
 * Class m171208_084229_create_table_messages
 */
class m171208_084229_create_table_messages extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%message}}', [

            'message_id' => $this->primaryKey()->notNull(),
            'message_subject' => $this->string(100)->notNull(),
            'message_creator_id' => $this->integer(11)->notNull(),
            'message_body' => $this->text()->notNull(),
            'message_parent_id' => $this->integer(11)->notNull(),            
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
        echo "m171208_084229_create_table_messages cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171208_084229_create_table_messages cannot be reverted.\n";

        return false;
    }
    */
}
