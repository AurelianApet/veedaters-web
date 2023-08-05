<?php

use yii\db\Migration;

/**
 * Handles the creation of table `video_map`.
 */
class m171004_075552_create_video_map_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%video_map}}', [
            'id' => $this->primaryKey(),
            'video_id' => $this->integer(),
            'user_id' => $this->integer(),
            'is_primary' => $this->boolean(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)
        ]);
        // creates index for column `author_id`
        $this->createIndex(
            'idx-video_map-video-id',
            '{{%video_map}}',
            'video_id'
        );

        // creates index for column `author_id`
        $this->createIndex(
            'idx-video_map-user-id',
            '{{%video_map}}',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-video_map-video-id',
            '{{%video_map}}',
            'video_id',
            '{{%videos}}',
            'id',
            'CASCADE'
        );
        
        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-video_map-user-id',
            '{{%video_map}}',
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
        $this->dropTable('{{%video_map}}');
    }
}
