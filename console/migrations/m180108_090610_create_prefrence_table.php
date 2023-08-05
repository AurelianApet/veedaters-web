<?php

use yii\db\Migration;

/**
 * Handles the creation of table `prefrence`.
 */
class m180108_090610_create_prefrence_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_preferences}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11),
            'gender' => $this->string(50),
            'min_age' => $this->integer(11),
            'max_age' => $this->integer(11),
            'distance' => $this->integer(11),
            'religion' => $this->string(50),
            'sports' => $this->string(255),
            'min_income' => $this->integer(11),
            'max_income' => $this->integer(11),
            'style' => $this->string(255),
            'alchohol' => $this->string(255),
            'smoke' => $this->string(255),
            'tatoo' => $this->string(255),
            'createddate' => $this->datetime(),
            'updateddate' => $this->datetime(),
        ]);
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user_preferences}}');
    }
}
