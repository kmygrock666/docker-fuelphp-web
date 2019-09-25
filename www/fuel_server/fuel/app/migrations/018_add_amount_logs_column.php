<?
namespace Fuel\Migrations;

class Add_amount_logs_Column
{

    function up()
    {
        \DBUtil::add_fields('amount_logs', array(
            'remark' => array('type' => 'varchar', 'constraint' => 10),
        ));
    }

    function down()
    {
       \DBUtil::drop_table('amount_logs');
    }
}