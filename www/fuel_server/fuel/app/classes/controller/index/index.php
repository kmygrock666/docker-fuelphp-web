<?php

use Fuel\Core\Debug;

class Controller_Index_Index extends Controller_Base
{
	public function get_index()
	{
		$widget = Request::forge('base/template')->execute();
		echo $widget;
	}
}
