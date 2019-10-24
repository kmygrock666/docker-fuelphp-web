<?php

use Fuel\Core\Response;

class Controller_Base extends Controller
{
    protected $redis;

    public function before()
    {
        //判斷是否已登入
        if ( ! Auth::check()) {
            Response::redirect('user/login');
        }
        $this->redis = Redis_Db::instance();
    }


}
