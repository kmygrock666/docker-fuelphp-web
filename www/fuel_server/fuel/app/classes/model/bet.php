<?php

class Model_Bet extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_belongs_to  = array('user');

    protected static $_properties = array(
        'id',
        'user_id',
        'bet_number',
        'status',
        'payout',
        'round_id',
        'period_id',
        'created_at',
        'updated_at',
        'type',
        'amount',
    );

    public static function find_bet($pid, $type, $round, $status)
    {
        return DB::select()->from('bets')->where('period_id', $pid)
        ->where('type', $type)->where('round_id', $round)->where('status', $status)->as_object("Model_Bet")->execute();
    }

    public static function find_bet_win($uid, $isWin, $round)
    {
        return DB::select()->from('bets')->where('user_id', $uid)
        ->where('status', $isWin)->where('round_id', $round)->as_object("Model_Bet")->execute();
    }

    public static function find_bet_userId($uid, $start, $end)
    {
        return Model_Bet::query()->where('user_id', $uid)
                                    ->and_where_open()->where('created_at', '>=', $start)
                                    ->where('created_at', '<=', $end)
                                    ->and_where_close()->order_by('id', 'desc')->limit(20)->get();
    }

    public static function insert_bet_LastId($user_id, $bet, $rid, $pid, $type, $amount)
    {
        $bet = Model_Bet::forge(array(
            'user_id' => $user_id,
            'bet_number' => $bet,
            'status' => 0,
            'payout' => 0,
            'round_id' => $rid,
            'period_id' => $pid,
            'type' => $type,
            'amount' => $amount,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));
        $result = $bet->save();
        if( ! $result)  return null;    
        
        return $bet->id;

    }

    public static function sum_member_by_condition($uid, $start, $end, $pid)
    {
        $expr_1 = DB::expr('sum(payout) as payout');
        $expr_2 = DB::expr('sum(amount) as amount');
        $expr_3 = DB::expr('count(user_id) as count');
        $query = DB::select('user_id', $expr_1, $expr_2, $expr_3)->from('bets');
        $query->where('status', ">", 0);
        if (is_null($pid))
        {
            if (!empty($uid)) $query->where('user_id', $uid);
            $query->and_where_open()->where('created_at', '>=', $start)->where('created_at', '<=', $end)->and_where_close();
        }
        else
        {
            $query->where('period_id', $pid);
        }
        $query->group_by('user_id');
        return $query->as_object('Model_Bet')->execute();
    }
}