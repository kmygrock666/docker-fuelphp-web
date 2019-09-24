<?php

class Controller_Base_Template extends Controller_Template
{

    // public $template = 'template_admin';

    public function action_index()
    {
        $data = array();
        array_push($data, array('title' => 'Home', 'href' => '/', 'active' => 'active', 'url' => ''));
        array_push($data, array('title' => 'Play', 'href' => '#', 'active' => '', 'url' => 'game/ulp'));

        $user = Auth::instance()->get_user_array();
        $this->template->title = 'LG';
        $this->template->nav = $data;
        $this->template->username = $user['screen_name'];
        $this->template->amount = $user['profile_fields']['amount'];
        $this->template->header = View::forge('baseTemplate/header');
        $this->template->content = View::forge('index/index');
        $this->template->footer = View::forge('baseTemplate/footer');
    }
}