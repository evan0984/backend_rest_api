<?php

use yii\db\Migration;
use api\models\User;

/**
 * Class m200709_020207_add_user_new_vields
 */
class m200709_020207_add_user_new_vields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'latitude', $this->string()->Null());
        $this->addColumn('{{%user}}', 'longitude', $this->string()->Null());
        $this->addColumn('{{%user}}', 'address', $this->string()->Null());
        $this->addColumn('{{%user}}', 'last_activity', $this->string()->Null());
        $this->addColumn('{{%user}}', 'premium', $this->smallInteger()->notNull()->defaultValue(User::NOT_PREMIUM));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'latitude');
        $this->dropColumn('{{%user}}', 'longitude');
        $this->dropColumn('{{%user}}', 'address');
        $this->dropColumn('{{%user}}', 'last_activity');
        $this->dropColumn('{{%user}}', 'premium');
    }
}
