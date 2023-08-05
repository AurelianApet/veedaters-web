<?php

use yii\db\Migration;

/**
 * Handles the creation of table `videos`.
 */
class m180919_000000_create_images_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
      $this->createTable('{{%images}}', [
        'id' => $this->primaryKey(),
        'image_url' => $this->string(),
        'image_title' => $this->string(),
        'image_description' => $this->string(),
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
        $this->dropTable('{{%images}}');
    }
}
