<?php

use Fuel\Core\Response;

class Controller_Base extends Controller
{
    public function before()
    {
        if( ! Auth::check()){
            Response::redirect('user/login');
        }
        // 檢查管理者
    }

	public function action_index()
	{
        // return Response::forge(View::forge('welcome/index'));
        // $data = array();
        // $this->template->title = 'Blog POST';
        // $this->template->content = View::forge('posts/index', $data);
    }
    
    public function action_add()
	{
        // $data = array();
        // $this->template->title = 'ADD POST';
        // $this->template->content = View::forge('posts/add', $data);
        // return Response::forge(View::forge('posts/add'));
	}

	
}
