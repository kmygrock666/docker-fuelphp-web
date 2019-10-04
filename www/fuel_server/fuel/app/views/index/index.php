<div class="starter-template">
    <h1>Weclome to the ultimate password game</h1>
    <p class="lead">
        <?php
            if(Auth::member(6)) echo 'no money, you can save money for anyone';
        ?>
        <!-- <br> 
        All you get is this text and a mostly barebones HTML document. -->
    </p>
    <div class="input-group mb-3" style="margin-left: 38%">
        <form id="save_form" onsubmit="return completeAndRedirect('save_form')">
        <?php
            if(Auth::member(6))
            {
                echo '<div class="input-group mb-3">';
                echo '<div class="input-group-prepend">';
                echo '<span class="input-group-text" id="basic-addon3">'.Lang::get("message.USERNAME").'</span>';
                echo '</div>';
                echo '<input type="text" class="form-control" name="account">';
                echo '</div>';

                echo '<div class="input-group mb-3">';
                echo '<div class="input-group-prepend">';
                echo '<span class="input-group-text" id="basic-addon3">'.Lang::get("message.AMOUNT").'</span>';
                echo '</div>';
                echo '<input type="text" class="form-control" name="money">';
                echo '</div>';
                echo '<select class="custom-select mr-sm-2 mb-3" name="type">';
                echo '<option value="3">存款</option>';
                echo '<option value="4">提款</option>';
                echo '</select>';
                echo '<button type="submit" class="btn btn-primary">確認</button>';
            }
        ?>
        </form>
    </div>
</div>

<script>
    function completeAndRedirect(form_id){
        console.log(form_id);
        console.log($('#' + form_id).serialize());
        $.ajax({
            url: 'api/inOutDeal/in',
            type: 'post',
            dataType: 'json',
            data: $('#' + form_id).serialize(),
            success: function(data) {
                // ... do something with the data..
                if(data.code == 0)
                {
                    alert(data.message);
                }
            }
        });
        return false;
    }
</script>