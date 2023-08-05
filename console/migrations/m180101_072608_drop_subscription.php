<?php

use yii\db\Migration;

/**
 * Class m180101_072608_drop_subscription
 */
class m180101_072608_drop_subscription extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         $this->dropTable('{{%subscription}}');
    }   

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180101_072608_drop_subscription cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180101_072608_drop_subscription cannot be reverted.\n";

        return false;
    }
    */
}
