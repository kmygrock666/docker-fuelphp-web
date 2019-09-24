<?php

class Model_User extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'username',
        'password',
        'group',
        'email',
        'last_login',
        'login_hash',
        'profile_fields',
        'created_at',
        'updated_at',
    );

    public static function find_by_username($name)
    {
        return Model_User::query()->where('username',$name)->get_one();
    }
}