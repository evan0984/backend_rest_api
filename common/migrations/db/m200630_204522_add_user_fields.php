<?php

use yii\db\Migration;

/**
 * Class m200630_204522_add_user_fields
 */
class m200630_204522_add_user_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'first_name', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'last_name', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'phone', $this->string(128)->Null()); 
        $this->addColumn('{{%user}}', 'gender', $this->integer(1)->Null()); 
        $this->addColumn('{{%user}}', 'image', $this->string(256)->Null());          
            
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'first_name');
        $this->dropColumn('{{%user}}', 'last_name');
        $this->dropColumn('{{%user}}', 'phone');
        $this->dropColumn('{{%user}}', 'gender');
        $this->dropColumn('{{%user}}', 'image');
    }
}
