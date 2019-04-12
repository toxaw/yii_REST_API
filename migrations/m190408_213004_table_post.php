<?php

use yii\db\Migration;

/**
 * Class m190408_213004_table_post
 */
class m190408_213004_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('post', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'anons' => $this->text(),
            'text' => $this->text(),
            'image' => $this->integer(1),
            'image' => $this->string(255),
            'datetime'  => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('post');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_213004_table_post cannot be reverted.\n";

        return false;
    }
    */
}
