<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tags`.
 */
class m171004_061521_create_tags_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%tags}}', [
            'id' => $this->primaryKey(),
            'tag' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)            
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%tags}}');
    }
}
