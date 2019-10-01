<?
namespace Fuel\Migrations;

class Create_bet
{

    function up()
    {
        \DBUtil::create_table('bets', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'uid' => array('type' => 'int', 'constraint' => 10, 'comment' => '用戶'),
            'type' => array('type' => 'varchar', 'constraint' => 3, 'comment' => '下注類型'),
            'bet_number' => array('type' => 'int', 'constraint' => 3, 'comment' => '下注號碼'),
            'amount' => array('type' => 'int', 'constraint' => 5, 'comment' => '下注金額'),
            'status' => array('type' => 'tinyint', 'constraint' => 1, 'comment' => '注單狀態'),
            'payout' => array('type' => 'double', 'comment' => '中獎金額'),
            'round_id' => array('type' => 'bigint', 'constraint' => 20, 'comment' => '下注局號'),
            'period_id' => array('type' => 'bigint', 'constraint' => 20, 'comment' => '期數'),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('bets');
    }
}