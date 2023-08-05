<?php

use yii\db\Migration;

/**
 * Handles the creation of table `meta_value`.
 */
class m171004_095315_create_meta_value_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
      $this->createTable('{{%meta_value}}', [
        'id' => $this->primaryKey(),
        'key_id' => $this->integer(),
        'meta_value' => $this->string(),
        'user_id' => $this->integer(),
        'created_at' => $this->dateTime(),
        'updated_at' => $this->dateTime(),
        'created_by' => $this->integer(11),
        'updated_by' => $this->integer(11)
      ]);
      
      // creates index for column `author_id`
      $this->createIndex(
          'idx-meta_value-key-id',
          '{{%meta_value}}',
          'key_id'
      );
      
      // creates index for column `author_id`
      $this->createIndex(
          'idx-meta_value-user-id',
          '{{%meta_value}}',
          'user_id'
      );

      // add foreign key for table `user`
      $this->addForeignKey(
          'fk-meta_value-key-id',
          '{{%meta_value}}',
          'key_id',
          '{{%metakey}}',
          'id',
          'CASCADE'
      );
      
      // add foreign key for table `user`
      $this->addForeignKey(
          'fk-meta_value-user-id',
          '{{%meta_value}}',
          'user_id',
          '{{%user}}',
          'id',
          'CASCADE'
      );
      
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%meta_value}}');
    }
}
