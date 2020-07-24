<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%friends}}`.
 */
class m200713_123713_create_friends_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%friend}}', [
            'id' => $this->primaryKey(),
            'user_source_id' => $this->integer()->notNull(),
            'user_target_id' => $this->integer()->notNull(),
            'show' => $this->smallInteger()->Null(),
            'created_at' => $this->string()->Null(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%friend}}');
    }
}
