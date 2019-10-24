<?php

use game\play\NumberPlay;

class Controller_Api_Timer extends Controller_Apibase
{
    protected $pid = "pid";

    public function get_001()
    {
        // $redis->del($this->pid);
        $pid = Config::get('myconfig.period.pid');
        $periodList = $this->redis->get($pid);
        var_dump($periodList);
        echo "<br>";
    }

    public function get_close()
    {
        $periodList = $this->redis->get($this->pid);
        if ($periodList == null) {
            return $this->response(array('code' => '1', 'message' => 'no data'));
        }
        $this->redis->del($this->pid);
        return $this->response(array('code' => '0', 'message' => 'Success delete'));
    }

    public function get_pusher()
    {
        // post.php ???
        // This all was here before  ;)
        $entryData = array(
            'category' => "kittensCategory",
            'title'    => "title_kittensCategory",
            'article'  => "article_kittensCategory",
            'when'     => time()
        );
//        \game\ws\WsPublish::send('up', 'bet', array());
//        \game\ws\WsPublish::send('up', 'period', array());
        \game\ws\WsPublish::send('up', 'winner', array(), array(), array(3));


    }
}