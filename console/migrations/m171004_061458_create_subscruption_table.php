<?php

use yii\db\Migration;

/**
 * Handles the creation of table `subscruption`.
 */
class m171004_061458_create_subscruption_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'subscription_type' => $this->string(),
            'expires_on' => $this->dateTime(),
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
        $this->dropTable('{{%subscription}}');
    }
}
