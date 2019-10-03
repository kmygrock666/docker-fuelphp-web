<?php


class Controller_Report_Report extends Controller_Base
{
    public function action_index()
    {
        Response::redirect('/');
    }

    public function get_report()
    {
        $start_get = Input::get('start', date('Y-m-d',time())." 00:00:00");
        $end_get = Input::get('end', date('Y-m-d')." 23:59:59");
        $user_id = Input::get('id', null);


        $start = strtotime($start_get);
        $end = strtotime($end_get);
        $betdata = Model_Bet::sum_member($user_id, $start, $end);

        foreach ($betdata as $bet)
        {
            $bet->username = $bet->user->username;
            $a = $bet->user;
            \Fuel\Core\Debug::dump($bet,$bet->user);

        }
        $data = array();
        $data['pdata'] = array();
        return View::forge('report/report', $data);
    }

    public function get_analysis()
    {
        $data = array();
        $data['pdata'] = array();
        return View::forge('report/report', $data);
    }
}