<?php

use yii\db\Migration;

/**
 * Class m171208_084545_create_table_messages_recipient
 */
class m171208_084545_create_table_messages_recipient extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%message_recipient}}', [
            'message_recipient_id' => $this->primaryKey()->notNull(),
            'message_id' => $this->integer(11)->notNull(),
            'is_read' => $this->integer(1)->notNull(),  
            'first_message_key' => $this->integer(1)->notNull(), 
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
        echo "m171208_084545_create_table_messages_recipient cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171208_084545_create_table_messages_recipient cannot be reverted.\n";

        return false;
    }
    */
}
