<?
namespace Fuel\Migrations;

class Add_Bet_Column
{

    function up()
    {
        \DBUtil::add_fields('bets', array(
            'type' => array('type' => 'varchar', 'constraint' => 3),
            'amount' => array('type' => 'int', 'constraint' => 5),
        ));
    }

    function down()
    {
       \DBUtil::drop_table('periods');
    }
}