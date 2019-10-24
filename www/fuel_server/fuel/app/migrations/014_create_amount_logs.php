<?php

namespace Fuel\Migrations;

class Create_amount_logs
{
	public function up()
	{
		\DBUtil::create_table('amount_logs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'type' => array('constraint' => 2, 'type' => 'int', 'comment' => '類型'),
			'remark' => array('type' => 'varchar', 'constraint' => 10, 'comment' => '備註'),
			'before_amount' => array('type' => 'double', 'comment' => '變動前餘額'),
			'amount' => array('type' => 'double', 'comment' => '金額'),
			'after_amount' => array('type' => 'double', 'comment' => '變動後餘額'),
			'user_id' => array('type' => 'int', 'constraint' => 10, 'comment' => '操作者'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('amount_logs');
	}
}