<?php

use yii\db\Migration;

class m171004_075925_insert_metakeys_data extends Migration
{
    public function safeUp()
    {
              
      $this->createTable('{{%metakey}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)            
      ]);
      
      
      $this->insert('{{%metakey}}', ['key' => 'orientation', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'skin_color', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'relationship_status', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'about_info', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'interested_in', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'hair_color', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'body_shape', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'height', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'ethnicity', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'education', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'profession', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'income', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'smoker', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'eye_color', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'body_type', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
      $this->insert('{{%metakey}}',['key' => 'looking_for', 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s'), 'created_by' => 1,'updated_by' => 1]);
    }

    public function safeDown()
    {
        echo "m171004_075925_insert_metakeys_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171004_075925_insert_metakeys_data cannot be reverted.\n";

        return false;
    }
    */
}
