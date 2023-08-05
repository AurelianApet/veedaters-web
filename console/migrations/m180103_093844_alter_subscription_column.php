<?php

use yii\db\Migration;

/**
 * Class m180103_093844_alter_subscription_column
 */
class m180103_093844_alter_subscription_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%subscription}}', 'amount', $this->float());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180103_093844_alter_subscription_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180103_093844_alter_subscription_column cannot be reverted.\n";

        return false;
    }
    */
}
