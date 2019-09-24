<?
namespace Fuel\Migrations;

class Round
{

    function up()
    {
        \DBUtil::create_table('rounds', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'openWin' => array('type' => 'int', 'constraint' => 3),
            'isWin' => array('type' => 'tinyint', 'constraint' => 1),
            'period_id' => array('type' => 'bigint', 'constraint' => 20),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('round');
    }
}