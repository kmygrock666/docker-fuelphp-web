<?php

class Model_Period extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'pid',
        'openWin',
        'isClose',
        'created_at',
        'updated_at',
    );

    public static function insert_Period($period, $openWin)
    {
        $p = Model_Period::forge(array(
            'pid' => $period,
            'openWin' => $openWin,
            'isClose' => false,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));

        $p->save();
        return $p->id;
    }
    
    public static function find_period($start, $end)
    {
        return Model_Period::query()->and_where_open()->where('created_at', '>=', $start)
        ->where('created_at', '<=', $end)
        ->and_where_close()->order_by('id', 'desc')->limit(20)->get();
    }

    public static function find_period_lastest()
    {
        return Model_Period::query()->where('isClose', 0)->order_by('created_at', 'desc')->get_one();
    }

}