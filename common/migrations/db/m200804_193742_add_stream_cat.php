<?php

use yii\db\Migration;

/**
 * Class m200804_193742_add_stream_cat
 */
class m200804_193742_add_stream_cat extends Migration
{
    public function up()
    {
        $this->addColumn('{{%stream}}', 'category', $this->string()->Null());
    }

    public function down()
    {
        $this->dropColumn('{{%stream}}', 'category');
    }
}
