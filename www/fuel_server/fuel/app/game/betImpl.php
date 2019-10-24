<?php
namespace game;

interface BetImpl {
    public function getResult($isSettle);
    public function getWinnerUser();
    // protected function init();
    // public function bet_number();
}