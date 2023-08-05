<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tag_map`.
 */
class m171004_064954_create_tag_map_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%tag_map}}', [
            'id' => $this->primaryKey(),
            'tag_id' => $this->integer(),
            'entity_id' => $this->integer(),
            'relationship' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)            
        ]);
        
        // creates index for column `author_id`
        $this->createIndex(
            'idx-tag_map-tag_id',
            '{{%tag_map}}',
            'tag_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-tag_map-tag_id',
            '{{%tag_map}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%tag_map}}');
    }
}
