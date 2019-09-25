<?php

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
}