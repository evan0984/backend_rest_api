<?php

use yii\db\Migration;

/**
 * Class m200630_221530_add_forgot_pass_login_pass
 */
class m200630_221530_add_forgot_pass_login_pass extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'forgot_sms_code', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'forgot_sms_code_exp', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'login_sms_code', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'login_sms_code_exp', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'reset_pass_code', $this->string(256)->Null());
        $this->addColumn('{{%user}}', 'verify_sms_code', $this->string(256)->Null());
            
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'forgot_sms_code');
        $this->dropColumn('{{%user}}', 'forgot_sms_code_exp');
        $this->dropColumn('{{%user}}', 'login_sms_code');
        $this->dropColumn('{{%user}}', 'login_sms_code_exp');
        $this->dropColumn('{{%user}}', 'reset_pass_code');
        $this->dropColumn('{{%user}}', 'verify_sms_code');
    }
}
