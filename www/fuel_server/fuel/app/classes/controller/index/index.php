<?php

use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Input;
use Fuel\Core\Lang;

class Controller_Index_Index extends Controller_Base
{
	public function get_index()
	{
		$lang = Input::get('lang', null);
		if ($lang != null) 
		{
			Session::set('lang', $lang);
			Response::redirect('/');
		}
		$widget = Request::forge('base/template')->execute();
		echo $widget;
	}
}
