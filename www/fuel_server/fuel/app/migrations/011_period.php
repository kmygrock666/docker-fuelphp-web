<?
namespace Fuel\Migrations;

class Period
{

    function up()
    {
        \DBUtil::create_table('periods', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'openWin' => array('type' => 'varchar', 'constraint' => 3),
            'isClose' => array('type' => 'tinyint', 'constraint' => 1),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('periods');
    }
}