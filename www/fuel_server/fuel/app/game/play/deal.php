<?php
namespace game\play;
use Model_Bet;
use Model_Amount_Log;
use Fuel\Core\DB;
use Auth;
use Exception;

class Deal 
{
    public function do_deal($amount, $user_id, $user_bet, $rid, $pid, $type){
        try
        {
            DB::start_transaction();
            //get user
            // $current_user = Model_User::find_by_username(Auth::get_screen_name());
            $current_user = Auth::get_profile_fields();
            if(count($current_user) == 0) throw new Exception('no loggin');
            $after_amount = $current_user['amount'] - $amount;
            //insert amount_log
            $amount_log_result = Model_Amount_Log::insert_amount_logs(1, $current_user['amount'], $amount * -1);
            //insert bet
            $bet_result = Model_Bet::insert_bet($user_id, $user_bet, $rid, $pid, $type, $amount);
            //update user
            Auth::update_user(
                array(
                    'amount' => $after_amount
                )
            );

            DB::commit_transaction();
            return $this->response_json(0, 'success');

        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            return $this->response_json(1, $e->getMessage());
        }
    }

    private function response_json($code, $message)
    {
        return array('code' => $code, 'message' => $message);
    }
}