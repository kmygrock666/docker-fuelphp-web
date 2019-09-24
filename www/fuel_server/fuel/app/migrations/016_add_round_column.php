<?
namespace Fuel\Migrations;

class Add_Round_Column
{

    function up()
    {
        \DBUtil::add_fields('rounds', array(
            'rate' => array('type' => 'double'),
        ));
    }

    function down()
    {
       \DBUtil::drop_table('rounds');
    }
}