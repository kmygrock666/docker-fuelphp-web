<?
namespace Fuel\Migrations;

class Create_period
{

    function up()
    {
        \DBUtil::create_table('periods', array(
            'id' => array('type' => 'bigint', 'constraint' => 20, 'auto_increment' => true),
            'open_win' => array('type' => 'varchar', 'constraint' => 3, 'comment' => '開獎號碼'),
            'is_close' => array('type' => 'tinyint', 'constraint' => 1, 'comment' => '開關盤'),
            'pid' => array('type' => 'varchar', 'constraint' => 12, 'comment' => '期數'),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('periods');
    }
}