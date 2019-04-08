<?php

use yii\db\Migration;

/**
 * Class m190408_164343_add_user_admin
 */
class m190408_164343_add_user_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('user', [
                    'login' => 'admin',
                    'password' => 'rostov2019',
                    'token' => 'lolkekcheburek',
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_164343_add_user_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_164343_add_user_admin cannot be reverted.\n";

        return false;
    }
    */
}
