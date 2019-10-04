<?php

class Model_User extends \Auth\Model\Auth_User 
{
    // protected static $_table_name = 'user';
//    protected static $_has_many = array('bet');

    protected static $_properties = array(
        'id',
        'username',
        'password',
        'group_id',
        'email',
        'last_login',
        'login_hash',
        'previous_login',
        'created_at',
        'updated_at',
        'user_id',
    );

    public static function find_by_username($name)
    {
        return Model_User::query()->where('username',$name)->get_one();
    }
}