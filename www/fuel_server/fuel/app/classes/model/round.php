<?php

use Fuel\Core\Debug;

class Model_Round extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'openWin',
        'isWin',
        'period_id',
        'created_at',
        'updated_at',
        'rate',
    );

    public static function insert_Round($period, $openWin, $rate)
    {
        $p = Model_Round::forge(array(
            'openWin' => $openWin,
            'isWin' => false,
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
        return Model_Round::query()->where("period_id", $period)->where("isWin", true)
                        ->and_where_open()->where('updated_at', '>=', $start)
                        ->where('updated_at', '<=', $end)
                        ->and_where_close()->get_one();
    }
}