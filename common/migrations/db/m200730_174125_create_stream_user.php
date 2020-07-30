<?php

use yii\db\Migration;

/**
 * Class m200730_174125_create_stream_user
 */
class m200730_174125_create_stream_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%stream_user}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'channel' => $this->string()->Null(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%stream_user}}');
    }
}
