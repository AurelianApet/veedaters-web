<?php

use yii\db\Migration;

/**
 * Class m180105_044830_create_table_notification
 */
class m180105_044830_create_table_notification extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        
        $this->createTable('{{%notification}}', [
            'notification_id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'device_type' => $this->string(50)->notNull(),
            'device_token' => $this->text()->notNull(),
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
        echo "m180105_044830_create_table_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180105_044830_create_table_notification cannot be reverted.\n";

        return false;
    }
    */
}
