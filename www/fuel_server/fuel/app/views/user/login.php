<div class="col-md-8">
    <h3>Login</h3>
</div>

<div class="col-md-8">
    <div class="card-sharp pad">
        <!----------------------------------- FORM ------------------------------->
        <?php echo Form::open('/user/login'); ?>
        <!----------------------------------- USERNAME ------------------------------->
        <div class="form-group">
            <?php echo Form::label('Username', 'username'); ?>
            <?php echo Form::input('username', Input::post('username'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- PASSWORD ------------------------------->
        <div class="form-group">
            <?php echo Form::label('PASSWORD', 'password'); ?>
            <?php echo Form::input('password', Input::post('password'), ['class' => 'form-control']); ?>
        </div>
        <!----------------------------------- SEND ------------------------------->
        <div class="actions">
            <?php echo Form::submit('LOGIN', 'LOGIN', ['class' => 'btn btn-primary']); ?>
        </div>
        <!----------------------------------- CLOSE FORM ------------------------------->
        <?php echo Form::close('/user/login'); ?>
        <p>Don't have an account? <a href="/user/register">Register</a>
        <p style="color:red"><?php echo Session::get_flash('error') ?></p>
    </div>

</div>