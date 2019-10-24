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
        //權限判斷
        $url = Input::uri();
        $geturl = explode("/", $url);
        $power = array('timer');
        if (in_array($geturl[2], $power)) {
            if (Auth::member(6)) {
                return true;
            }
        } else {
            if (Auth::check()) {
                return true;
            }
        }

        return false;
    }


}
