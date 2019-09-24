<?
namespace Fuel\Migrations;

class Add_Period_Column
{

    function up()
    {
        \DBUtil::add_fields('periods', array(
            'pid' => array('type' => 'varchar', 'constraint' => 12),
        ));
    }

    function down()
    {
       \DBUtil::drop_table('periods');
    }
}