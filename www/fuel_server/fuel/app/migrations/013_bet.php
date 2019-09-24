<?
namespace Fuel\Migrations;

class Bet
{

    function up()
    {
        \DBUtil::create_table('bets', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'uid' => array('type' => 'int', 'constraint' => 10),
            'bet_number' => array('type' => 'int', 'constraint' => 3),
            'isWin' => array('type' => 'tinyint', 'constraint' => 1),
            'payout' => array('type' => 'double'),
            'round_id' => array('type' => 'bigint', 'constraint' => 20),
            'period_id' => array('type' => 'bigint', 'constraint' => 20),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('bets');
    }
}