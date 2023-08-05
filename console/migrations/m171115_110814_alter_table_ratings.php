<?php

use yii\db\Migration;

/**
 * Class m171115_110814_alter_table_ratings
 */
class m171115_110814_alter_table_ratings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('{{%ratings}}', 'like');
        $this->dropColumn('{{%ratings}}', 'dislike');
        $this->addColumn('{{%ratings}}', 'review', $this->integer(1)->after('user_id'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171115_110814_alter_table_ratings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171115_110814_alter_table_ratings cannot be reverted.\n";

        return false;
    }
    */
}
