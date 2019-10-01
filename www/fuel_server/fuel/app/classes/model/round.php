<?php

use Fuel\Core\Debug;

class Model_Round extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'open_win',
        'is_settle',
        'period_id',
        'created_at',
        'updated_at',
        'rate',
    );

    public static function insert_Round($period, $openWin, $rate)
    {
        $p = Model_Round::forge(array(
            'open_win' => $openWin,
            'is_settle' => false,
            'period_id' => $period,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
            'rate' => json_encode($rate),
        ));

        $p->save();
        return $p;
    }

    public static function find_by_id($id)
    {
        return Model_Round::query()->where('id', $id)->get_one();
    }

    public static function find_by_period($period)
    {
        return Model_Round::query()->where("period_id", $period)->order_by('created_at', 'desc')->get();
    }

    public static function find_by_open($period)
    {
        $start = strtotime("-10 second");
        $end = strtotime("+10 second");
        return Model_Round::query()->where("period_id", $period)->where("is_settle", 2)
                        ->and_where_open()->where('updated_at', '>=', $start)
                        ->where('updated_at', '<=', $end)
                        ->and_where_close()->get_one();
    }

    public static function save_settle_status($round)
    {
        $round = Model_Round::find_by_id($round);
        $round->is_settle = 2;
        $round->updated_at = strtotime('now');
        $round->save();
    }

    public static function save_status($round, $number)
    {
        $round = Model_Round::find_by_id($round);
        $round->open_win = $number;
        $round->is_settle = true;
        $round->save();
    }
}