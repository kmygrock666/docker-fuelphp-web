<?php

namespace Fuel\Migrations;

class Create_amount_logs
{
	public function up()
	{
		\DBUtil::create_table('amount_logs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'type' => array('constraint' => 2, 'type' => 'int'),
			'before_amount' => array('type' => 'double'),
			'amount' => array('type' => 'double'),
			'after_amount' => array('type' => 'double'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('amount_logs');
	}
}