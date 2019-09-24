<?php

class Model_Bet extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'uid',
        'bet_number',
        'isWin',
        'payout',
        'round_id',
        'period_id',
        'created_at',
        'updated_at',
        'type',
        'amount',
    );

    public static function find_bet($pid, $type, $round)
    {
        return DB::select()->from('bets')->where('period_id', $pid)
        ->where('type', $type)->where('round_id', $round)->as_object("Model_Bet")->execute();
    }

    public static function insert_bet($user_id, $bet, $rid, $pid, $type, $amount)
    {
        $bet = Model_Bet::forge(array(
            'uid' => $user_id,
            'bet_number' => $bet,
            'isWin' => 0,
            'payout' => 0,
            'round_id' => $rid,
            'period_id' => $pid,
            'type' => $type,
            'amount' => $amount,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));
        $result = $bet->save();
        return $result;

    }
}