<div>
    <div>
        <?php echo Lang::get('games.PERIOD'); ?>
        <span id="pid"><?php echo $period; ?></span>
        <span id="time" style="position:absolute;right:200px">倒数 <?php echo $time % 60; ?></span>
        <div>
        <p><?php echo Lang::get('games.RATIO'); ?> </p>
            <span><?php echo Lang::get('games.ULTIMATE_PASSWORD'); ?> <span id="n"><?php echo $rate->n; ?></span></span>&nbsp;
            <span><?php echo Lang::get('games.SINGLE'); ?> <span id="s"><?php echo $rate->s; ?></span></span>&nbsp;
            <span><?php echo Lang::get('games.DOUBLE'); ?> <span id="d"><?php echo $rate->d; ?></span></span>&nbsp;
        </div>
        <div id="round" style="float:left">
            <?php
                foreach($round_number as $k => $r)
                {
                    echo '<p>' + ($k + 1) + Lang::get('games.ROUND_AWARD') + $r+ ' </p>';
                }
            ?>
        </div>
        <div id="history" style="float:right"></div>
    </div>
    <div class="ball">
        <?php
        for ($i = 1; $i <= $total; $i++) {
            $class = "disablediv";
            if($i >= $min and $i <= $max) $class = '';
            echo '<div class="circle '.$class.'" id="b' . $i . '"><a href="#" onClick="selected(' . $i . ')"><span>' . $i . '</span></a></div>';
        }
        ?>
    </div>
    <div class="single">
        <div class="circle" id="single"><a href="#" onClick="selected('single')"><span><?php echo Lang::get('games.SINGLE'); ?></span></a></div>
        <div class="circle" id="double"><a href="#" onClick="selected('double')"><span><?php echo Lang::get('games.DOUBLE'); ?></span></a></div>
    </div>
</div>
<div class="se">
        <div class="input-group mb-3" style="float:left;">
            <div class="input-group-prepend">
                <label class="input-group-text" for="inputGroupSelect01"><?php echo Lang::get('games.AMOUNT'); ?></label>
            </div>
            <select class="custom-select" id="inputGroupSelect01">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
            <button type="button" class="btn btn-primary ml-3" onclick="sendAll()"><?php echo Lang::get('games.CONFIRM'); ?></button>
            <button type="button" class="btn btn-primary ml-3" onclick="clearAll(true)"><?php echo Lang::get('games.CLEAR'); ?></button>
        </div>
        
        <!-- <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-default">下注金额</span>
            </div>
            <input type="text" id="inputGroupSelect01" value="1" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
        </div> -->
    <div>

    <button type="button" class="btn btn-primary" onclick="change(1)"><?php echo Lang::get('games.NUMBER'); ?></button>
    <button type="button" class="btn btn-info" onclick="change(2)"><?php echo Lang::get('games.SD'); ?></button>
</div>
<?php echo Asset::js('cs.js')?>
<script>
    var status = true;
    var open_result = true;
    var last_period_enable = true;
    var selectPlayType = 1;
    var total_amount = 0;
    var lang = <?php echo $lang; ?>;
    wsConnect(<?php echo $userid; ?>);

    $(function() {
        // getLastPeriod();
        // if(st_) getStatus();
        // st_ = false;
    });

    async function wsConnect(id){
        await connect('ws://localhost:8080', '{"tp": 0,"user_id": ' + id + '}');
        subscribeTopic("bet", bet_callback);
        subscribeTopic("period", period_callback);
        subscribeTopic("winner", winner_callback);
    }

    function bet_callback(topic, data){
        console.log("bet_callback : " + topic + " / " + data.title);
    }

    function period_callback(topic, data){
        console.log("period_callback : " + topic + " / " + data.title);
    }

    function winner_callback(topic, data){
        console.log("winner_callback : " + topic + " / " + data.title);
    }


    function enable_ball(min, max) {
        $('.ball>div').addClass("disablediv");
        $('.ball>div').removeClass("selected");
        $('.single>div').removeClass("selected");
        disableElement(false);
        for (var i = min; i <= max; i++) {
            $('#b' + i).removeClass("disablediv");
            $('#b' + i).removeClass("winPwd");
        }
    }

    function change(cmd) {
        
        if (cmd == 1) {
            $('.single').hide();
            $('.ball').show();
            selectPlayType = 1;
        } else {
            $('.single').show();
            $('.ball').hide();
            selectPlayType = 2;
        }
        clearAll(true);
    }

    function getStatus() {
        $.ajax({
            url: "api/games/st",
            type: 'get',
            dataType: "json",
        }).done(function(response) {
            // console.log(response);
            setTimeout("getStatus()", 1000);
            if(response.code > 0)
            {
                console.log(response.message);
            }
            else
            {
                var d = response.data;
                console.log(d);
                if(d.close == false)
                {
                    if (d.status == "bet")
                    {
                        $('#time').text(lang.time+ " " + d.time);
                        refresh(d);
                        if(last_period_enable)
                            getLastPeriod();
                        status = true;
                        open_result = true;
                    }
                    else if(d.status == "stop")
                    {
                        $('#time').text(lang.settle + " " + d.time);
                        status = false;
                        runOpen();
                    }
                }
                else
                {
                    $('#time').text(lang.next + " " + d.time);
                    $('#b' + d.pwd).addClass("winPwd");
                    refresh(d);
                    runOpen();
                    last_period_enable = true;
                }
                
            }
        });

    }

    function refresh(d){
        if($('#round>p').length != d.round_number.length)
        {
            $('#pid').text(d.pid);
            enable_ball(d.min, d.max);
            $('#n').text(d.rate.n);
            $('#s').text(d.rate.s);
            $('#d').text(d.rate.d);
            update_round(d.round_number);
            clearAll(true);
        }
    }

    function update_round(round_number){
        $('#round').html('');
        var html = '';
        round_number.forEach(function(e, index){
            let sd = (e % 2 == 0) ? lang.single : lang.double;
            html += '<p>' + (index + 1) + lang.round_arawd + '： '+ e + "/" + sd + ' </p>';
        });
         
        $('#round').append(html);
    }

    function update_last_period(period) {
        $('#history').html('');
        var html = '<p>'+ lang.previous + ' : ' + period.pid;
        html += '<p> ' + lang.ultimate_password +' ：' + period.open + '</p>';
        period.round.forEach(function(e, index){
            let sd = (e % 2 == 0) ? lang.single : lang.double;
            html += '<p>' + (index + 1) + lang.round_arawd + '： '+ e + "/" + sd + ' </p>';
        });
        
        $('#history').append(html);
        last_period_enable = false;
    }

    function runOpen(){
        if(open_result)
        {
            getResult();
        }
    }

    function send(number) {
        let type = 1;
        if (isNaN(number)) type = 2;
        let amount = $('#inputGroupSelect01').val();
        if (checkAmount(amount)) 
        {
            alert(lang.insufficient_balance);
            return;
        }
        if (doubleCheck("下注"))
        {
            $.ajax({
                url: "api/bet/send",
                type: 'get',
                dataType: "json",
                data:{'b':number, 't': type, m: amount},
                success: function (msg) {
                    if(msg.code == 0)
                    {
                        var object = msg.data;
                        resfreshBalance(object.amount);
                        alert(msg.message);
                    }
                    else
                    {
                        alert(msg.message);
                    }
                    console.log(msg);
                }
            })
        }
    }

    function getResult() {
        $.ajax({
            url: "api/bet/result",
            type: 'get',
            dataType: "json",
            data:{},
            success: function (msg) {
                console.log(msg);
                if(msg.code == 0)
                {
                    open_result = false;
                    var object = msg.data;
                    if(object.length > 0)
                    {
                        object.forEach(function(e){
                            addBalance(e['payout']);
                            var text = e['num'];
                            console.log(e);
                            if(e['type'] == 2)
                            {
                                if(e['num'] == 0) text = lang.double;
                                else text = lang.single;
                            }
                            alert(lang.bet_data+": " + text + " ,"+ lang.bet_data+ ": " + e['payout']);
                            
                        })
                    }
                }
                else
                {
                    console.log(msg.message);
                }
            },
            error: function(error){
                console.log(error);
            }
        })
    }

    function getLastPeriod() {
        $.ajax({
            url: "api/games/lastPeriod",
            type: 'get',
            dataType: "json",
            data:{},
            success: function (msg) {
                console.log(msg);
                if(msg.code == 0)
                {
                    update_last_period(msg.data);
                }
                else
                {
                    console.log(msg.message);
                }
            },
            error: function(error){
                console.log(error);
            }
        })
    }

    function doubleCheck(message){
        var msg = message;
        if (confirm(msg) == true){ 
            return true; 
        }else{ 
            return false; 
        } 
    }

    function selected(number){
        
        let amount = $('#inputGroupSelect01').val();
        if (checkAmount(number)) 
        {
            alert(lang.insufficient_balance);
            return;
        }
        disableElement(true);
        let idx = "#b" + number;
        if (isNaN(number)) idx = "#" + number;
        if($(idx).hasClass('selected'))
        {
            $(idx).removeClass("selected");
            addBalance(amount);
            total_amount -= parseFloat(amount);
        }
        else
        {
            $(idx).addClass("selected");
            addBalance(amount * -1);
            total_amount += parseFloat(amount);
        }
    }

    function sendAll(){
        let amount = $('#inputGroupSelect01').val();
        let data = new Array();
        let showData = '';
        let win = 0;
        let lose = 0;
        let profit = 0;
        let n_rate = $('#n').text();
        let s_rate = $('#s').text();
        let d_rate = $('#d').text();

        if (selectPlayType == 1)
        {
            showData = lang.number + " : ";
            for (var i = 1; i <= 40; i++) {
                if ($('#b' + i).hasClass("selected")){
                    data.push(i);
                    showData += i + ",";
                }
            }
            win = n_rate * amount;
            lose = data.length * amount;
            profit = (win - lose).toFixed(3);
        }
        else
        {
            showData = lang.sd + " : ";
            let s_w = 0;
            let d_w = 0;
            if($('#single').hasClass("selected")) {
                data.push('s');
                showData += lang.single + ",";
                s_w = (s_rate * amount).toFixed(3);
                win =  s_w + "/";
            }

            if($('#double').hasClass("selected")){
                data.push('d');
                showData += lang.double + ",";
                d_w = (d_rate * amount).toFixed(3);
                win += d_w + "/";
            }
            win = win.substring(0, win.lastIndexOf("/"));
            lose = data.length * amount;
            if(s_w > 0 && d_w > 0){
                profit = (s_w - lose).toFixed(3) + "/" + (d_w - lose).toFixed(3);
            }else if(s_w > 0){
                profit = (s_w - lose).toFixed(3);;
            }else if(d_w > 0){
                profit = (d_w - lose).toFixed(3);;
            }
        }

        if(data.length == 0) {
            alert(lang.not_bet);
            return;
        }

        let lastindex = showData.lastIndexOf(",");
        if (lastindex > -1)
            showData = showData.substring(0, lastindex);


        showData += "/ " + lang.lose + ": " + lose;
        showData += "/ " + lang.win + ": " + win;
        showData += "/ " + lang.profit + ": " + profit;

        if (doubleCheck(showData))
        {
            $.ajax({
                url: "api/bet/sends",
                type: 'post',
                dataType: "json",
                data:{b : data, t : selectPlayType, m : amount},
                success: function (msg) {
                    if(msg.code == 0)
                    {
                        var object = msg.data;
                        resfreshBalance(object.amount);
                        alert(msg.message);
                        clearAll(false);
                    }
                    else
                    {
                        alert(msg.message);
                        clearAll(true);
                    }
                    console.log(msg);
                }
            })
        }
    }

    function clearAll(boolean){
        if(boolean) addBalance(total_amount);
        disableElement(false);
        total_amount = 0;
        if(selectPlayType == 1)
        {
            $('.ball>div').removeClass("selected");
        }
        else
        {
            $('.single>div').removeClass("selected");
        }

    }

    function disableElement(bool){
        $('#inputGroupSelect01').attr('disabled', bool);
    }
</script>