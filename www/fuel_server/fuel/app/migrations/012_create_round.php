<?
namespace Fuel\Migrations;

class Create_round
{

    function up()
    {
        \DBUtil::create_table('rounds', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'open_win' => array('type' => 'int', 'constraint' => 3, 'comment' => '單雙號碼'),
            'is_settle' => array('type' => 'tinyint', 'constraint' => 1, 'comment' => '結算'),
            'period_id' => array('type' => 'bigint', 'constraint' => 20, 'comment' => '期數'),
            'rate' => array('type' => 'varchar', 'constraint' => 50, 'comment' => '賠率'),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('round');
    }
}