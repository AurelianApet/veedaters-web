<?php

use yii\db\Migration;

/**
 * Class m171113_130630_create_table_photo
 */
class m171113_130630_create_table_photo extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%photos}}', [

            'photos_id' => $this->primaryKey()->notNull(),
            'photo_type' => $this->string(100)->notNull(),
            'photo_title' => $this->string(100)->notNull(),
            'photo_path' => $this->string(255)->notNull(),
            'photo_details' => $this->string(500),
            'isactive' => $this->boolean()->notNull()->defaultValue(1),
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
        echo "m171113_130630_create_table_photo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171113_130630_create_table_photo cannot be reverted.\n";

        return false;
    }
    */
}
