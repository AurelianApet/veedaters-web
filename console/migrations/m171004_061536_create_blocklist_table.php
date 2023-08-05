<?php

use yii\db\Migration;

/**
 * Handles the creation of table `blocklist`.
 */
class m171004_061536_create_blocklist_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%blocklist}}', [
            'id' => $this->primaryKey(),
            'blocked_id' => $this->integer(),
            'blocked_by_id' => $this->integer(),
            'is_blocked' => $this->boolean(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)            
        ]);
        
        // creates index for column `author_id`
        $this->createIndex(
            'idx-blocklist-blocked_id',
            '{{%blocklist}}',
            'blocked_id'
        );
        // creates index for column `author_id`
        $this->createIndex(
            'idx-blocklist-blocked_by_id',
            '{{%blocklist}}',
            'blocked_by_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-blocklist-blocked_id',
            '{{%blocklist}}',
            'blocked_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-blocklist-blocked_by_id',
            '{{%blocklist}}',
            'blocked_by_id',
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
        $this->dropTable('{{%blocklist}}');
    }
}
