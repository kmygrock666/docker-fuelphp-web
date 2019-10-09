<?php

use Fuel\Core\Debug;
use Fuel\Core\Lang;

class Controller_Base_Template extends Controller_Template
{

    // public $template = 'template_admin';

    public function action_index()
    {
        $data = array();
        if (Auth::member(1) || Auth::member(6))
        {
            array_push($data, array('title' => 'message.HOME', 'href' => '/', 'active' => 'active', 'url' => ''));
            array_push($data, array('title' => 'message.PLAY', 'href' => '#', 'active' => '', 'url' => 'game/ulp'));
            array_push($data, array('title' => 'message.DEAL_RECORD', 'href' => '#', 'active' => '', 'url' => 'mem/search/deal'));
            array_push($data, array('title' => 'message.BET_RECORD', 'href' => '#', 'active' => '', 'url' => 'mem/search/record'));
            array_push($data, array('title' => 'message.PERIOD_SEARCH', 'href' => '#', 'active' => '', 'url' => 'mem/search/period'));
        }

        if (Auth::member(6))
        {
            array_push($data, array('title' => 'message.REPORT', 'href' => '#', 'active' => '', 'url' => 'report/report/report'));
//            array_push($data, array('title' => 'message.ANALYSIS', 'href' => '#', 'active' => '', 'url' => 'report/report/analysis'));
        }

        $user_profile_fields = Auth::get_profile_fields();
        $this->template->title = 'LG';
        $this->template->nav = $data;
        $this->template->lang = "message.".strtoupper(Lang::get_lang());
        $this->template->url = Session::get_flash('url');
        $this->template->username = $user_profile_fields['nickname'];
        $this->template->amount = number_format($user_profile_fields['amount'],3);
        $this->template->header = View::forge('baseTemplate/header');
        $this->template->content = Presenter::forge('index/index');
        $this->template->footer = View::forge('baseTemplate/footer');
    }
}