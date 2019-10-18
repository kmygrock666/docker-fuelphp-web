<?php

use ___PHPSTORM_HELPERS\object;
use Fuel\Core\Debug;

class Presenter_Game_Ulp extends Presenter
{
	public function view()
	{
        $lang = array(
            'time' => Lang::get('games.TIMMING'),
            'settle' => Lang::get('games.SETTLE'),
            'next' => Lang::get('games.NEXT'),
            'single' => Lang::get('games.SINGLE'),
            'double' => Lang::get('games.DOUBLE'),
            'round_arawd' => Lang::get('games.ROUND_AWARD'),
            'previous' => Lang::get('games.PREVIOUS'),
            'ultimate_password' => Lang::get('games.ULTIMATE_PASSWORD'),
            'insufficient_balance' => Lang::get('games.INSUFFICIENT_BALANCE'),
            'bet_data' => Lang::get('games.BET_DATA'),
            'bet_amount' => Lang::get('games.BET_AMOUNT'),
            'number' => Lang::get('games.NUMBER'),
            'sd' => Lang::get('games.SD'),
            'not_bet' => Lang::get('games.NOT_BET'),
            'lose' => Lang::get('games.LOSE'),
            'win' => Lang::get('games.WIN'),
            'profit' => Lang::get('games.PROFIT'),
        );

        $this->set('lang', json_encode($lang), false);
	}
}
