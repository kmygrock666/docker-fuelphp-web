<?php
use Fuel\Core\Presenter;

class Presenter_Index_Index extends  Presenter
{
    public function view()
    {
        $html = '';
        $direction = '';
        if(Auth::member(6))
        {
            $html .= '<div class="input-group mb-3">';
            $html .= '<div class="input-group-prepend">';
            $html .= '<span class="input-group-text" id="basic-addon3">'.Lang::get("message.USERNAME").'</span>';
            $html .= '</div>';
            $html .= '<input type="text" class="form-control" name="account">';
            $html .= '</div>';

            $html .= '<div class="input-group mb-3">';
            $html .= '<div class="input-group-prepend">';
            $html .= '<span class="input-group-text" id="basic-addon3">'.Lang::get("message.AMOUNT").'</span>';
            $html .= '</div>';
            $html .= '<input type="text" class="form-control" name="money">';
            $html .= '</div>';
            $html .= '<select class="custom-select mr-sm-2 mb-3" name="type">';
            $html .= '<option value="3">'.Lang::get("games.IN").'</option>';
            $html .= '<option value="4">'.Lang::get("games.OUT").'</option>';
            $html .= '</select>';
            $html .= '<button type="submit" class="btn btn-primary">'.Lang::get("games.CONFIRM").'</button>';

            $direction = Lang::get("games.DIRECTION");
            $welcome = Lang::get("message.WELCOME");
        }
        $this->welcome = $welcome;
        $this->set('power', $html,false);
        $this->set('direction', $direction,false);
    }
}