<?php

use yii\db\Migration;

/**
 * Class m171113_121915_create_table_user_meta
 */
class m171113_121915_create_table_user_meta extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         $this->createTable('{{%user_meta}}', [
            'user_meta_id' => $this->primaryKey()->notNull(),
            'meta_key' => $this->string(50)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'meta_value' => $this->text()->notNull(),
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
        echo "m171113_121915_create_table_user_meta cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_121915_create_table_user_meta cannot be reverted.\n";

        return false;
    }
    */
}
