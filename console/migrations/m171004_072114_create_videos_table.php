<?php

use yii\db\Migration;

/**
 * Handles the creation of table `videos`.
 */
class m171004_072114_create_videos_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
      $this->createTable('{{%videos}}', [
        'id' => $this->primaryKey(),
        'user_id'=> $this->integer(11),
        'video_url' => $this->string(),
        'video_title' => $this->string(),
        'video_description' => $this->string(),
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
        $this->dropTable('{{%videos}}');
    }
}
