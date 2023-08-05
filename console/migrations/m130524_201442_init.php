<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'gender' => $this->integer(),
            'dob' => $this->date(),
            'country' => $this->string(),
            'orientation' => $this->integer(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'is_approved' => $this->boolean(),
            'address_id' => $this->integer(),
            'subscription' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->insert('{{%user}}', [
              'username' => 'admin',
              'email' => 'admin@veedater.com',
              'gender' => '1',
              'dob' => '1980-01-01',
              'country' => 'india',
              'orientation' => '0',
              'auth_key' => 'kjabdjkasbdaskjdbaskjdbsakj',
              'password_hash' => Yii::$app->security->generatePasswordHash('digi#123'),
              'password_reset_token' => null,
              'status' => true,
              'is_approved' => true,
              'address_id' => null,
              'subscription' => null,
              'created_at' => date('Y-m-d h:i:s'),
              'updated_at' => date('Y-m-d h:i:s'),
              'created_by' => 1,
              'updated_by' => 1
          ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
