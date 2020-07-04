<?php

use yii\db\Migration;

/**
 * Class m200701_114228_add_user_birthday
 */
class m200701_114228_add_user_birthday extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'birthday', $this->string(256)->Null());
            
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'birthday');
    }
}
