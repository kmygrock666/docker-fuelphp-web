<?php

use Fuel\Core\DB;
use Fuel\Core\Input;
use Fuel\Core\Lang;

class Controller_Api_InOutDeal extends Controller_Apibase
{
    public function post_in()
    {
        $operate_type = array(3,4); //3 存款 4 提款

        $account = Input::post('account', null);
        $money = Input::post('money', null);
        $type = Input::post('type', null);

        if ((is_null($account) || is_null($money) || is_null($type)) or (empty($account) || empty($money) || empty($type)))
            return $this->response(array('code' => '1', 'message' => Lang::get('error.ER1')));
        if (!is_numeric($money))
            return $this->response(array('code' => '2', 'message' => Lang::get('error.ER2')));
        if ( ! in_array($type, $operate_type))
            return $this->response(array('code' => '3', 'message' => Lang::get('error.ER3')));

        if($type == 4)
        {
            $money = $money * -1;
        }

        try {
            DB::start_transaction();

            $user = Model_User::find_by_username($account);
            if (is_null($user)) throw new Exception(Lang::get('error.ER4'));
            $before_amount = $user->amount;
            $after_amount = $before_amount + $money;
            $user->amount = $after_amount;
            $user->save();

            Model_Amount_Log::insert_amount_logs($type, $before_amount, $money, 0, $user->id);

            DB::commit_transaction();

        } catch (Exception $e) {

            DB::rollback_transaction();
            \Fuel\Core\Log::error('inoutdeal, line 46 error : '.$e->getMessage());
            return $this->response(array('code' => '3', 'message' => Lang::get('error.ER5')));
        }

        return $this->response(array('code' => '0', 'message' => Lang::get('error.ER4'), 'data' => array('after' => $after_amount)));
    }
}