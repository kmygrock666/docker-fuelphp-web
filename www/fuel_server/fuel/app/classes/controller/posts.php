<?php

class Controller_Posts extends Controller_Template
{
	public function action_index()
	{
        // return Response::forge(View::forge('welcome/index'));
        $data = array();
        $this->template->title = 'Blog POST';
        $this->template->content = View::forge('posts/index', $data);
    }
    
    public function action_add()
	{
        $data = array();
        $this->template->title = 'ADD POST';
        $this->template->content = View::forge('posts/add', $data);
        // return Response::forge(View::forge('posts/add'));
	}

	
}
