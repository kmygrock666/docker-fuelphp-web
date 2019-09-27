<?php

class Controller_Base_Template extends Controller_Template
{

    // public $template = 'template_admin';

    public function action_index()
    {
        $data = array();
        array_push($data, array('title' => 'Home', 'href' => '/', 'active' => 'active', 'url' => ''));
        array_push($data, array('title' => 'Play', 'href' => '#', 'active' => '', 'url' => 'game/ulp'));
        array_push($data, array('title' => '交易纪录', 'href' => '#', 'active' => '', 'url' => 'mem/search/deal'));
        array_push($data, array('title' => '下注纪录', 'href' => '#', 'active' => '', 'url' => 'mem/search/record'));
        array_push($data, array('title' => '期数查询', 'href' => '#', 'active' => '', 'url' => 'mem/search/period'));

        $user_profile_fields = Auth::get_profile_fields();
        $this->template->title = 'LG';
        $this->template->nav = $data;
        $this->template->username = $user_profile_fields['nickname'];
        $this->template->amount = round($user_profile_fields['amount'],4);
        $this->template->header = View::forge('baseTemplate/header');
        $this->template->content = View::forge('index/index');
        $this->template->footer = View::forge('baseTemplate/footer');
    }
}