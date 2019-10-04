<?php

use Auth\Auth;
use Fuel\Core\DB;
use Fuel\Core\Debug;
use Fuel\Core\Input;
use game\play\Deal;

class Controller_Api_InOutDeal extends Controller_Apibase
{
    public function post_in()
    {
        $operate_type = array(3,4); //3 存款 4 提款

        $account = Input::post('account', null);
        $money = Input::post('money', null);
        $type = Input::post('type', null);

        if ((is_null($account) || is_null($money) || is_null($type)) or (empty($account) || empty($money) || empty($type)))
            return $this->response(array('code' => '1', 'message' => 'params is error'));
        if (!is_numeric($money))
            return $this->response(array('code' => '2', 'message' => 'amount is not number'));
        if ( ! in_array($type, $operate_type))
            return $this->response(array('code' => '3', 'message' => 'undefine type'));

        $message = 'save money success';
        if($type == 4)
        {
            $money = $money * -1;
            $message = 'withdrawal money success';
        }

        try {
            DB::start_transaction();

            $user = Model_User::find_by_username($account);
            if (is_null($user)) throw new Exception('no account');
            $before_amount = $user->amount;
            $after_amount = $before_amount + $money;
            $user->amount = $after_amount;
            $user->save();

            Model_Amount_Log::insert_amount_logs($type, $before_amount, $money, 0, $user->id);

            DB::commit_transaction();

        } catch (Exception $e) {

            DB::rollback_transaction();
            return $this->response(array('code' => '3', 'message' => $e->getMessage()));
        }

        return $this->response(array('code' => '0', 'message' => $message));
    }
}