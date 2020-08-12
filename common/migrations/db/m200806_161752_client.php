<?php

use yii\db\Migration;
/**
 * Class m200806_161752_client
 */
class m200806_161752_client extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(32),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(40)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'oauth_client' => $this->string(),
            'oauth_client_user_id' => $this->string(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'logged_at' => $this->integer()
        ]);

        $this->addColumn('{{%client}}', 'first_name', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'last_name', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'phone', $this->string(128)->Null()); 
        $this->addColumn('{{%client}}', 'gender', $this->integer(1)->Null()); 
        $this->addColumn('{{%client}}', 'image', $this->string(256)->Null()); 
        $this->addColumn('{{%client}}', 'forgot_sms_code', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'forgot_sms_code_exp', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'login_sms_code', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'login_sms_code_exp', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'reset_pass_code', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'verify_sms_code', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'birthday', $this->string(256)->Null());
        $this->addColumn('{{%client}}', 'latitude', $this->string()->Null());
        $this->addColumn('{{%client}}', 'longitude', $this->string()->Null());
        $this->addColumn('{{%client}}', 'address', $this->string()->Null());
        $this->addColumn('{{%client}}', 'last_activity', $this->string()->Null());
        $this->addColumn('{{%client}}', 'premium', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%client}}', 'bio', $this->text()->Null()); 
    }

    public function safeDown()
    {
        $this->dropTable('{{%client}}');
    }
}
