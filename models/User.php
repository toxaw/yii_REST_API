<?php

namespace app\models;

use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $token
 */
class User extends ActiveRecord 
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'password', 'token'], 'string', 'max' => 255],
        ];
    }

    public function auth()
    {
       return $this->findOne(['login' => $this->login, 'password' => $this->password])->token ?? false;
    }

    public static function findIdentityByAccessToken($token)
    {
        return static::findOne(['token' => $token]);
    }
}
