<?php
namespace game\play;
use game\play\SDPlay;
use game\play\NumberPlay;

class UltimatPassword
{
    private  static $instance;
    protected $games = array();

    private function __construct() { }
    private function __clone() { }

    public static function getInstance()
    {
        if ( ! self::$instance)
            self::$instance = new UltimatPassword();
        return self::$instance;
    }

    public function create_play($pid, $r, $ans, $max, $min, $number)
    {
        if(count($this->games) == 0)
        {
            $this->games['SDP'] = new SDPlay($pid, $r, $ans, $max, $min, $number);
            $this->games['NP'] = new NumberPlay($pid, $r, $ans, $max, $min, $number);
        }
        else
        {
            $this->games['SDP']->setGameParams($pid, $r, $ans, $max, $min, $number);
            $this->games['NP']->setGameParams($pid, $r, $ans, $max, $min, $number);
        }
        
    }

    public function settle($name)
    {
        if (array_key_exists($name, $this->games))
        {
            return $this->games[$name]->getResult();
        }
        return null;
    }

    public function getRate($name)
    {
        if (array_key_exists($name, $this->games))
        {
            return $this->games[$name]->getRate();
        }
        return null;
    }


}