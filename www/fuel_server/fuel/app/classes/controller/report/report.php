<?php


class Controller_Report_Report extends Controller_Base
{
    public function action_index()
    {
        Response::redirect('/');
    }

    public function get_report()
    {
        $date = Input::get('date', null);
        $username = Input::get('account', '');
        $perid = Input::get('pid', null);

        $data = array();
        $betdata = array();
        $start = '';
        $end = '';
        $user_id = '';
        $flag = true;
        if (is_null($perid))
        {
            //區間查詢
            if (is_null($date) and empty($username)) { $flag = false; }
            else
            {
                if (is_null($date))
                {
                    $start_get = date('Y-m-d',time())." 00:00:00";
                    $end_get = date('Y-m-d')." 23:59:59";
                }
                else
                {
                    $date = explode("-", $date);
                    $start_get = $date[0];
                    $end_get = $date[1];
                }

                if ( ! empty($username))
                {
                    $user = Model_User::find_by_username($username);
                    if (is_null($user)) $flag = false;
                    else $user_id = $user->id;

                    $data['username'] = $username;
                }

                $start = strtotime($start_get);
                $end = strtotime($end_get);
                $data['date'] = $start_get." - ".$end_get ;

            }
        }
        else
        {
            $data['pid'] = $perid;
        }

        if($flag)
        {
            $betdata = Model_Bet::sum_member_by_condition($user_id, $start, $end, $perid);

            foreach ($betdata as $bet)
            {
                $bet->account = $bet->user->username;
                $bet->profit = $bet->payout - $bet->amount;
            }

            $betdata = $betdata->as_array();
        }

        $data['betdata'] = $betdata;

        return View::forge('report/report', $data);
    }

    public function get_analysis()
    {
        $data = array();
        $data['pdata'] = array();
        return View::forge('report/report', $data);
    }
}