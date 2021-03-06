<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $create_date
 * @property string $last_login
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups(){
        return $this->hasMany(UsersGroup::className(), ['id_user' => 'id']);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'username', 'email', 'password'], 'string'],
            [['create_date', 'last_login'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'create_date' => 'Create Date',
            'last_login' => 'Last Login',
        ];
    }
}
