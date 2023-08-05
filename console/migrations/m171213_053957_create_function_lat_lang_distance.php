<?php

use yii\db\Migration;

class m171213_053957_create_function_lat_lang_distance extends Migration
{
    public function safeUp()
    {
    //     $this->execute("CREATE FUNCTION `LAT_LNG_DISTANCE`(`lat1` FLOAT, `lng1` FLOAT, `lat2` FLOAT, `lng2` FLOAT) RETURNS float
    //     NO SQL
    // RETURN ROUND(3959 * 2 * ASIN(SQRT(
    //         POWER (SIN((lat1 - abs(lat2)) * pi()/180 / 2),
    //         2) + COS(lat1 * pi()/180 ) * COS(abs(lat2) * 
    //         pi()/180) * POWER(SIN((lng1 - lng2) * 
    //         pi()/180 / 2), 2) )), 2)");
    }

    public function safeDown()
    {
        echo "m171213_053957_create_function_lat_lang_distance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171213_053957_create_function_lat_lang_distance cannot be reverted.\n";

        return false;
    }
    */
}
