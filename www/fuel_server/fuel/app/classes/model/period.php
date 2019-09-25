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

        $result = $p->save();
        return $result;
    }
    
    public static function find_logs($start, $end)
    {
        return Model_Period::query()->and_where_open()->where('created_at', '>=', $start)
        ->where('created_at', '<=', $end)
        ->and_where_close()->order_by('id', 'desc')->limit(20)->get();
    }

}