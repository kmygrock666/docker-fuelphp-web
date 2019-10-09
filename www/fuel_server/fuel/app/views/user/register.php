<div class="col-md-8">
    <h3><?php echo Lang::get('message.REGISTER'); ?></h3>
</div>

<div class="col-md-8">
    <div class="card-sharp pad">
        <!----------------------------------- FORM ------------------------------->
        <?php echo Form::open('/user/register'); ?>
        <!----------------------------------- USERNAME ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.USERNAME'), 'username'); ?>
            <?php echo Form::input('username', Input::post('username'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- EMAIL ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.EMAIL'), 'email'); ?>
            <?php echo Form::input('email', Input::post('email'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- PASSWORD ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.PASSWORD'), 'password'); ?>
            <?php echo Form::input('password', Input::post('password'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- CONFIRM PASSWORD ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.CONFIRM_PASSWORD'), 'password'); ?>
            <?php echo Form::input('password_confirm', Input::post('confirm'), ['class' => 'form-control']); ?>
        </div>
        <!----------error---------->
        <p style="color:red"><?php echo Session::get_flash('error') ?></p>
        <!----------------------------------- SEND ------------------------------->
        <div class="actions">
            <?php echo Form::submit('REGISTER', Lang::get('message.REGISTER'), ['class' => 'btn btn-primary']); ?>
        </div>
        <!----------------------------------- CLOSE FORM ------------------------------->
        <?php echo Form::close('/user/register'); ?>
        <p><?php echo Lang::get('message.LOGIN_MESSAGE'); ?><a href="/user/login"><?php echo Lang::get('message.LOGIN'); ?></a>

    </div>

</div>