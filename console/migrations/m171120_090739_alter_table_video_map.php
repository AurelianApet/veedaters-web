<?php

use yii\db\Migration;

/**
 * Class m171120_090739_alter_table_video_map
 */
class m171120_090739_alter_table_video_map extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn('{{%video_map}}', 'created_at', 'createddate');
        $this->renameColumn('{{%video_map}}', 'updated_at', 'updateddate');
        $this->renameColumn('{{%video_map}}', 'created_by', 'createdby');
        $this->renameColumn('{{%video_map}}', 'updated_by', 'updatedby');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171120_090739_alter_table_video_map cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171120_090739_alter_table_video_map cannot be reverted.\n";

        return false;
    }
    */
}
