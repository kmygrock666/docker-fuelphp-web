<?php

class Controller_Index_Index extends Controller_Base
{
	public function action_index()
	{
		$widget = Request::forge('base/template')->execute("development");
		echo $widget;
	}
}
