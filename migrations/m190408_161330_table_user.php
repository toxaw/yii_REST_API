<?php

use yii\db\Migration;

/**
 * Class m190408_161330_table_user
 */
class m190408_161330_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('user', [
            'id' => $this->primaryKey(),
            'login' => $this->string(255),
            'password' => $this->string(255),
            'token' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_161330_table_user cannot be reverted.\n";

        return false;
    }
    */
}
