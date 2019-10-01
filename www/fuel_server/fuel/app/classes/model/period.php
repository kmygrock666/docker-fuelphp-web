<?php

class Model_Period extends Orm\Model 
{
    // protected static $_table_name = 'user';
    protected static $_has_many = array('round');

    protected static $_properties = array(
        'id',
        'pid',
        'open_win',
        'is_close',
        'created_at',
        'updated_at',
    );

    public static function insert_Period($period, $openWin)
    {
        $p = Model_Period::forge(array(
            'pid' => $period,
            'open_win' => $openWin,
            'is_close' => false,
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

    public static function find_period_lastest($enable)
    {
        return Model_Period::query()->where('is_close', $enable)->order_by('created_at', 'desc')->get_one();
    }

    public static function find_period_maxid()
    {
        return Model_Period::query()->max('id');;
    }

    public static function save_period_status($id)
    {
        $period = Model_Period::find_by_id($id);
		$period->is_close = true;
		return $period->save();
    }

}