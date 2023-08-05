<?php

use yii\db\Migration;

/**
 * Class m171114_115950_create_table_review
 */
class m171114_115950_create_table_review extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%ratings}}', [
            'rating_id' => $this->primaryKey(),
            'like' => $this->integer()->notNull(),
            'dislike' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
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
        echo "m171114_115950_create_table_review cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171114_115950_create_table_review cannot be reverted.\n";

        return false;
    }
    */
}
