<div class="col-md-8">
    <h3><?php echo Lang::get('message.LOGIN'); ?></h3>
</div>

<div class="col-md-8">
    <div class="card-sharp pad">
        <!----------------------------------- FORM ------------------------------->
        <?php echo Form::open('/user/login'); ?>
        <!----------------------------------- USERNAME ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.USERNAME'), 'username'); ?>
            <?php echo Form::input('username', Input::post('username'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- PASSWORD ------------------------------->
        <div class="form-group">
            <?php echo Form::label(Lang::get('message.PASSWORD'), 'password'); ?>
            <?php echo Form::input('password', Input::post('password'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- SEND ------------------------------->
        <div class="actions">
            <?php echo Form::submit('LOGIN', Lang::get('message.LOGIN'), ['class' => 'btn btn-primary']); ?>
        </div>
        <!----------------------------------- CLOSE FORM ------------------------------->
        <?php echo Form::close('/user/login'); ?>
        <p><?php echo Lang::get('message.REGISTER_MESSAGE'); ?><a href="/user/register"><?php echo Lang::get('message.REGISTER'); ?></a>
        <p style="color:red"><?php echo Session::get_flash('error') ?></p>
    </div>

</div>