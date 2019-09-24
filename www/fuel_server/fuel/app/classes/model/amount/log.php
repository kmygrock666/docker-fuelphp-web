<?php

class Model_Amount_Log extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'type',
		'before_amount',
		'amount',
		'after_amount',
		'created_at',
		'updated_at',
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

	public static function insert_amount_logs($type, $before_amount, $amount)
    {
        $bet = Model_Amount_Log::forge(array(
            'type' => $type,
            'before_amount' => $before_amount,
            'amount' => $amount,
            'after_amount' => $before_amount + $amount,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));
        $result = $bet->save();
        return $result;

    }

}
