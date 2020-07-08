<?php

use yii\db\Migration;

/**
 * Class m200708_022754_add_swife_tables
 */
class m200708_022754_add_swife_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%like}}', [
            'id' => $this->primaryKey(),
            'user_source_id' => $this->integer()->notNull(),
            'user_target_id' => $this->integer()->notNull(),
            'like' => $this->integer()->notNull(),
            'created_at' => $this->string()->Null(),
        ], $tableOptions);


    }


    public function down()
    {
        $this->dropTable('{{%like}}');
    }
}
