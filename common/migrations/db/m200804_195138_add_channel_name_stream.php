<?php

use yii\db\Migration;

/**
 * Class m200804_195138_add_channel_name_stream
 */
class m200804_195138_add_channel_name_stream extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stream}}', 'name', $this->string()->Null());
    }

    public function down()
    {
        $this->dropColumn('{{%stream}}', 'name');
    }
}
