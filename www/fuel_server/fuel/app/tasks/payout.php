<?php

namespace Fuel\Tasks;
use Fuel\Core\Redis_Db;
use Model_Period;
use Model_Round;
use game\play\NumberPlay;
use Fuel\Core\Autoloader;

class Payout
{
	protected static $pid = "pid";

	public static function run($val)
	{
        Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/sDPlay.php');
        $game_sD = new SDPlay($val->pid, $val->round, $val->pwd, $val->max, $val->min);
        $game_sD->getResult();

        Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/numberPlay.php');
        $game_number = new NumberPlay($val->pid, $val->round, $val->pwd, ($val->max - $val->min + 1));
        if($game_number->getResult())
        {
            return true;
        }
        echo "PAYOUT";
        return false;
	}
}

/* End of file tasks/robots.php */
