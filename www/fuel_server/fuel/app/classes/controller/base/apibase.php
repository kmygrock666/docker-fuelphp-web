<?php

use Fuel\Core\Response;

class Controller_Apibase extends Controller_Rest
{
    protected $redis;

    public function before()
    {
        parent::before();
        $this->redis = Redis_Db::instance();
    }

    // public static function _init() { }

    public function authCheck()
    {
        if(Auth::check()){
            return true;
        }
        return false;
        // 檢查管理者
    }

	
}
