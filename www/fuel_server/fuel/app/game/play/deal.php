<?php
namespace game\play;
use Model_Bet;
use Model_User;
use Model_Amount_Log;
use Fuel\Core\DB;
use Auth;
use Exception;
use Fuel\Core\Debug;

class Deal 
{
    //amount_logs 1:下注,2:派彩
    public function bet_deal($amount, $user_id, $user_bet, $rid, $pid, $type){
        try{
            DB::start_transaction();
            
            //get user amount
            $user_id = Auth::get_user_id();
            $current_user = Auth::get_profile_fields();
            if(count($current_user) == 0) throw new Exception('no loggin');

            $before_amount = $current_user['amount'];
            $after_amount =  $before_amount - $amount;
            if($after_amount < 0) throw new Exception('balance not enough');
            //update user
            Auth::update_user(
                array(
                    'amount' => $after_amount
                )
            );

            //insert bet
            $bet_id = Model_Bet::insert_bet_LastId($user_id[1], $user_bet, $rid, $pid, $type, $amount);
            if($bet_id == null) throw new Exception('insert data error ,no bet id');

            //insert amount_log
            $amount_log_result = Model_Amount_Log::insert_amount_logs(1, $before_amount, $amount * -1, $bet_id, $user_id[1]);
            

            DB::commit_transaction();
            return $this->response_json(0, 'success', array('amount' => $after_amount));

        }catch (Exception $e){
            DB::rollback_transaction();
            return $this->response_json(1, $e->getMessage());
        }
    }

    public function bet_deal_bacth($amount, $user_id, $user_bet, $rid, $pid, $type){
        try{
            DB::start_transaction();
            
            //get user amount
            $user_id = Auth::get_user_id();
            $current_user = Auth::get_profile_fields();
            if(count($current_user) == 0) throw new Exception('no loggin');

            $total_amount = $amount * count($user_bet);
            $before_amount = $current_user['amount'];
            $after_amount =  $before_amount - $total_amount;
            if($after_amount < 0) throw new Exception('balance not enough');
            //update user
            Auth::update_user(
                array(
                    'amount' => $after_amount
                )
            );

            foreach($user_bet as $b)
            {
                //insert bet
                $bet_id = Model_Bet::insert_bet_LastId($user_id[1], $b, $rid, $pid, $type, $amount);
                if($bet_id == null) throw new Exception('insert data error ,no bet id');
                //insert amount_log
                Model_Amount_Log::insert_amount_logs(1, $before_amount, $amount * -1, $bet_id, $user_id[1]);
                $before_amount -= $amount;
            }
            
            

            DB::commit_transaction();
            return $this->response_json(0, 'success', array('amount' => $after_amount));

        }catch (Exception $e){
            DB::rollback_transaction();
            Log::error("deal.php line 87 ".$e->getMessage());
            return $this->response_json(1, $e->getMessage());
        }
    }

    public function send_bonus($bet, $payout)
    {
        try
        {
            DB::start_transaction();
            
            //insert bet
            $bet->status = true;
            $bet->payout = $payout;
            $bet->save();
            //get user amount
            $user = Model_User::find($bet->user_id);
            if($user == null) throw new Exception('no user');

            $before_amount = $user->amount;
            $after_amount = $user->amount + $payout;
            //update user
            $user->amount = $after_amount;
            $user->save();
            //insert amount_log
            $amount_log_result = Model_Amount_Log::insert_amount_logs(2, $before_amount, $payout, 0, $user->id);
            

            DB::commit_transaction();
            return $this->response_json(0, 'success');

        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            Log::error("deal.php line 121 ".$e->getMessage());
            return $this->response_json(1, $e->getMessage());
        }
    }

    private function response_json($code, $message, $data = array())
    {
        return array('code' => $code, 'message' => $message, 'data' => $data);
    }
}