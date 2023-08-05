<?php

use yii\db\Migration;

class m171004_101714_create_relationship_mysql extends Migration
{
    public function safeUp()
    {
        // creates index for column `author_id`
        $this->createIndex(
            'idx-meta-value-user_id',
            '{{%meta_value}}',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-meta-value-user_id',
            '{{%meta_value}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        // creates index for column `author_id`
        $this->createIndex(
            'idx-subscription-user_id',
            '{{%subscription}}',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-subscription-user_id',
            '{{%subscription}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        echo "m171004_101714_create_relationship_mysql cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171004_101714_create_relationship_mysql cannot be reverted.\n";

        return false;
    }
    */
}
