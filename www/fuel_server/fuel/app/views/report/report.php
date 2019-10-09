<div class="input-group mb-3">
    <form id="p_form" onsubmit="return completeAndRedirect('p_form')">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-default"><?php echo Lang::get('report.PERIOD'); ?></span>
            </div>
            <input type="text" class="form-control" name="pid">
            <div class="input-group-append" id="button-addon4">
                <button class="btn btn-primary" type="sumit"><?php echo Lang::get('report.SEARCH'); ?></button>
            </div>
        </div>
    </form>
    <form id="d_form" onsubmit="return completeAndRedirect('d_form')" style="width: 70% !important;">
        <div class="input-group mb-3">
            <div class="col-sm-7">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroup-sizing-default"><?php echo Lang::get('report.DATE_INTERVAL'); ?></span>
                    </div>
                    <input type="text" class="form-control" name="date" id="date">
                </div>
            </div>
            <div class="col-sm-5">
                <label class="sr-only" for="inlineFormInputGroupUsername">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo Lang::get('report.MEMBER_ACCOUNT'); ?></div>
                    </div>
                    <input type="text" class="form-control" name="account" id="inlineFormInputGroupUsername" placeholder="Username">
                    <div class="input-group-prepend">
                        <button class="btn btn-info" type="sumit"><?php echo Lang::get('report.SEARCH'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="text-align: center">
<?php
    if(isset($pid)) echo "<p>".Lang::get('report.PERIOD')." : ".$pid."</p>";
    else{
        if (isset($date))echo "<p>".Lang::get('report.DATE_INTERVAL')." : ".$date."</p>";
        if (isset($username)) echo "<p>".Lang::get('report.MEMBER_ACCOUNT')." : ".$username."</p>";
    }
?>
</div>

<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col"><?php echo Lang::get('report.MEMBER_ACCOUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('report.TOTAL_BET_COUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('report.TOTAL_BET_AMOUNT'); ?></th>
      <th scope="col"><?php echo Lang::get('report.TOTAL_PROFIT'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php

      foreach($betdata as $b)
      {
        echo "<tr>";
        echo "<th scope='row'>".$b->account."</th>";
        echo "<td>".$b->count."</td>";
        echo "<td>".$b->amount."</td>";
        echo "<td>".$b->profit."</td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>
<script>
    $(function() {
        $("#date").daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY/MM/DD HH:mm'
            }
        });
    })

    function completeAndRedirect(form_id){
        // console.log(form_id);
        // console.log($('#' + form_id).serialize());
        touch('report/report/report?' + $('#' + form_id).serialize());
        return false;
    }
</script>