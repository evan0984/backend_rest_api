<?php

use yii\db\Migration;

/**
 * Class m200703_133758_add_chat_tables
 */
class m200703_133758_add_chat_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%chat}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->Null(),
            'user_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%party}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'chat_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->integer()->Null(),
            'text' => $this->text(),
            'image' => $this->string()->Null(),
            'created_at' => $this->string()->Null(),
        ], $tableOptions);
    }


    public function down()
    {
        $this->dropTable('{{%chat}}');
        $this->dropTable('{{%party}}');
        $this->dropTable('{{%message}}');
    }
}
