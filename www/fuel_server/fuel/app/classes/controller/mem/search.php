<?php

use Fuel\Core\Debug;
use Fuel\Core\Input;

class Controller_Mem_Search extends Controller_Base
{
    public function get_index()
    {
        return Response::redirect('/');
    }

    public function get_record()
    {
        //1.號碼 2.單雙
        $types = array('1' => Lang::get('games.NUMBER'), '2' => Lang::get('games.SD'));
        //0.未開講 1.中獎 2.未中獎 3.不開獎
        $winType = array(
            '0' => Lang::get('games.NOT_OPEN'),
            '1' => Lang::get('games.WIN'),
            '2' => Lang::get('games.NOT_WIN'),
            '3' => Lang::get('games.NOT_LOST_NOT_WIN')
        );
        //0.單 1.雙
        $singleOrDouble = array('0' => Lang::get('games.DOUBLE'), '1' => Lang::get('games.SINGLE'));

        $data = array();
        $user_id = Auth::get_user_id();
        $start_get = Input::get('start', date('Y-m-d', time()) . " 00:00:00");
        $end_get = Input::get('end', date('Y-m-d') . " 23:59:59");

        $start = strtotime($start_get);
        $end = strtotime($end_get);

        $data['betdata'] = Model_Bet::find_bet_userId($user_id[1], $start, $end);

        foreach ($data['betdata'] as $bet) {
            $bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
            $bet->bet_number = $bet->type == 1 ? $bet->bet_number : $singleOrDouble[$bet->bet_number];
            $bet->type = $types[$bet->type];
            $bet->status = $winType[$bet->status];
        }

        return View::forge('mem/record', $data);
    }

    public function get_deal()
    {   // 1.下注 2.派彩 3.存款 4.提款 5.不判斷輸贏
        $deal_types = array(
            '1' => Lang::get('games.BET'),
            '2' => Lang::get('games.PAYOUT'),
            '3' => Lang::get('games.IN'),
            '4' => Lang::get('games.OUT'),
            '5' => Lang::get('games.NOT_LOST_NOT_WIN')
        );

        $user_id = Auth::get_user_id();
        $start_get = Input::get('start', date('Y-m-d', time()) . " 00:00:00");
        $end_get = Input::get('end', date('Y-m-d') . " 23:59:59");

        $start = strtotime($start_get);
        $end = strtotime($end_get);

        $data = array();

        $data['dealdata'] = Model_Amount_Log::find_logs_userId($user_id[1], $start, $end);


        foreach ($data['dealdata'] as $bet) {
            $bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
            $bet->type = $deal_types[$bet->type];
        }

        return View::forge('mem/deal', $data);
    }

    public function get_period()
    {
        //0.開盤中 1.關盤
        $period_types = array(
            '0' => Lang::get('games.OPEN_PERIOD'),
            '1' => Lang::get('games.CLOSE_PERIOD')
        );

        $start_get = Input::get('start', date('Y-m-d', time()) . " 00:00:00");
        $end_get = Input::get('end', date('Y-m-d') . " 23:59:59");

        $start = strtotime($start_get);
        $end = strtotime($end_get);

        $data = array();

        $data['pdata'] = Model_Period::find_period($start, $end);
        foreach ($data['pdata'] as $bet) {
            $round = $bet->round;
            $bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
            if ($bet->is_close == 0) {
                $bet->open_win = '';
            }
            $bet->is_close = $period_types[$bet->is_close];
            $bet->round_open = array();
            $bet->round_ratio = array();
            $bet->round_id = array();
            foreach ($round as $k => $r) {
                if ($r->is_settle == 2) {
                    array_push($bet->round_open, $r->open_win);
                }

                array_push($bet->round_ratio, json_decode($r->rate, true));
                array_push($bet->round_id, $r->id);
            }
        }


        return View::forge('mem/period', $data);
    }
}
