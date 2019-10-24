<?php

use Fuel\Core\Response;

class Controller_User extends Controller_Template
{
    public $template = 'template_login';

    public function get_index()
    {
        Response::redircet('user/login');
    }

    public function action_login()
    {
        if (Auth::check()) {
            Response::redirect('/');
        }

        if (Input::method() == 'POST') {
            $val = Validation::forge();
            $val->add_field('username', 'Your username', 'required|min_length[3]|max_length[50]');
            $val->add_field('password', 'Your password', 'required|min_length[3]|max_length[50]');
            if ($val->run()) {
                $auth = Auth::instance();
                if ($auth->login($val->validated('username'), $val->validated('password'))) {
                    Session::set_flash('success', 'You have logged in');
                    Response::redirect('/');
                } else {
                    Session::set_flash('error', 'no user account or password error');
                    Response::redirect('user/login');
                }
            } else {
                Session::set_flash('error', 'Login Error');
                Response::redirect('user/login');
            }
        } else {
            $data = [];
            $this->template->title = 'Login';
            $this->template->header = View::forge('baseTemplate/header');
            $this->template->footer = View::forge('baseTemplate/footer');
            $this->template->content = View::forge('user/login', $data);

        }
    }

    public function get_logout()
    {
        if (Auth::check()) {
            Auth::logout();
            Session::set_flash('success', 'You are logged out');
            Response::redirect('/');
        } else {
            Response::redirect('/');
        }
    }

    public function action_register()
    {
        if (Auth::check()) {
            Response::redirect('/');
        }

        if (Input::method() == 'POST' && Input::post('REGISTER')) {
            $val = Validation::forge();
            $val->add_field('username', 'Your username', 'required|min_length[3]|max_length[50]');
            $val->add_field('email', 'Your email', 'required|min_length[3]|max_length[50]');
            $val->add_field('password', 'Your password', 'required|min_length[3]|max_length[50]');
            $val->add_field('password_confirm', 'Confirm your password', 'required|min_length[3]|max_length[50]');
            if ($val->run()) {
                $auth = Auth::instance();
                try {
                    $create_user = $auth->create_user(
                        $val->validated('username'),
                        $val->validated('password'),
                        $val->validated('email'),
                        1,
                        array('nickname' => $val->validated('username'), 'amount' => 500)
                    );
                    if ($create_user) {
                        Session::set_flash('success', 'User created');
                        $auth = Auth::instance();
                        if ($auth->login($val->validated('email'), $val->validated('username')) | Auth::check()) {
                            $current_user = Model_User::find_by_username(Auth::get_screen_name());
                            Session::set_flash('success', 'Welcome ' . $current_user->username);
                            Response::redirect('');
                        } else {
                            Response::redirect('user/login');
                        }
                    } else {
                        Session::set_flash('error', 'There was an error creating a new user');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', $e->getMessage());
                    $data['error'] = $val->error();
                    $this->template->title = 'Registed';
                    $this->template->header = View::forge('baseTemplate/header');
                    $this->template->footer = View::forge('baseTemplate/footer');
                    $this->template->content = View::forge('user/register', $data);
                }
            } else {
                Session::set_flash('error', 'There has an error input');
                $data['error'] = $val->error();
                $this->template->title = 'Registed';
                $this->template->header = View::forge('baseTemplate/header');
                $this->template->footer = View::forge('baseTemplate/footer');
                $this->template->content = View::forge('user/register', $data);
            }
        } else {
            $data = [];
            $this->template->title = 'Registed';
            $this->template->header = View::forge('baseTemplate/header');
            $this->template->footer = View::forge('baseTemplate/footer');
            $this->template->content = View::forge('user/register', $data);
        }
    }
}
