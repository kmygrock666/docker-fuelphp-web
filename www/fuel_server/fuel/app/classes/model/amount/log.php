<?php

class Model_Amount_Log extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'type',
		'before_amount',
		'amount',
		'after_amount',
		'remark',
		'created_at',
		'updated_at',
		'operate',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'amount_logs';

	public static function insert_amount_logs($type, $before_amount, $amount, $remark, $operate)
    {
        $bet = Model_Amount_Log::forge(array(
			'remark' => $remark,
            'type' => $type,
            'before_amount' => $before_amount,
            'amount' => $amount,
            'after_amount' => $before_amount + $amount,
            'created_at' => strtotime('now'),
			'updated_at' => strtotime('now'),
			'operate' => $operate,
		));
        $result = $bet->save();
        return $result;

	}
	
	public static function find_logs_userId($uid, $start, $end)
    {
        return Model_Amount_Log::query()->where('operate', $uid)
                                    ->and_where_open()->where('created_at', '>=', $start)
                                    ->where('created_at', '<=', $end)
                                    ->and_where_close()->order_by('id', 'desc')->limit(20)->get();
    }

}
