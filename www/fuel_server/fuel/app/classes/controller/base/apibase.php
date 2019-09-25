<?php

use Fuel\Core\Response;

class Controller_Apibase extends Controller_Rest
{
    public function authCheck()
    {
        if(Auth::check()){
            return true;
        }
        return false;
        // 檢查管理者
    }

	
}
