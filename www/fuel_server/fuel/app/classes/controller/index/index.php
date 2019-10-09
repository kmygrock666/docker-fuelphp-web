<?php

use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Input;
use Fuel\Core\Lang;
use game\play\UltimatPassword;

class Controller_Index_Index extends Controller_Base
{
	public function get_index()
	{
		$lang = Input::get('lang', null);
		if ($lang != null) 
        {
            $url = Input::get('url', null);
			Session::set('lang', $lang);
            if( ! is_null($url))
            {
                Session::set_flash('url', $url);
                Response::redirect("/");
            }

		}
		$widget = Request::forge('base/template')->execute();
		echo $widget;
	}

}
