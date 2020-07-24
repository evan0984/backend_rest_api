<?php

use yii\db\Migration;

/**
 * Class m200710_132450_add_bio_images_table
 */
class m200710_132450_add_bio_images_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'bio', $this->text()->Null());

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->smallInteger()->notNull(),
            'avator' => $this->smallInteger()->Null(),
            'created_at' => $this->string()->notNull(),
            'path' => $this->string()->notNull(),
            'sort' => $this->smallInteger()->Null(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'bio');
        $this->dropTable('{{%images}}');
    }
}
