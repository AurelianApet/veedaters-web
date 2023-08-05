<?php

use yii\db\Migration;

/**
 * Class m171120_090205_alter_table_video
 */
class m171120_090205_alter_table_video extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn('{{%videos}}', 'created_at', 'createddate');
        $this->renameColumn('{{%videos}}', 'updated_at', 'updateddate');
        $this->renameColumn('{{%videos}}', 'created_by', 'createdby');
        $this->renameColumn('{{%videos}}', 'updated_by', 'updatedby');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171120_090205_alter_table_video cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171120_090205_alter_table_video cannot be reverted.\n";

        return false;
    }
    */
}
