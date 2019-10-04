<div>
    <div>
        <?php echo Lang::get('game.PERIOD'); ?>
        <span id="pid"><?php echo $period; ?></span>
        <span id="time" style="position:absolute;right:200px">倒数 <?php echo $time % 60; ?></span>
        <div>
        <p>赔率 </p>
            <span>终极密码<span id="n"><?php echo $rate->n; ?></span></span>&nbsp;
            <span>单<span id="s"><?php echo $rate->s; ?></span></span>&nbsp;
            <span>双<span id="d"><?php echo $rate->d; ?></span></span>&nbsp;
        </div>
        <div id="round" style="float:left">
            <?php
                foreach($round_number as $k => $r)
                {
                    echo '<p> 第 ' + ($k + 1) + ' 回合 獎號： ' + $r+ ' </p>';
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
        <div class="circle" id="single"><a href="#" onClick="selected('single')"><span>单</span></a></div>
        <div class="circle" id="double"><a href="#" onClick="selected('double')"><span>双</span></a></div>
    </div>
</div>
<div class="se">
        <div class="input-group mb-3" style="float:left;">
            <div class="input-group-prepend">
                <label class="input-group-text" for="inputGroupSelect01">下注金额</label>
            </div>
            <select class="custom-select" id="inputGroupSelect01">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
            <button type="button" class="btn btn-primary ml-3" onclick="sendAll()">確認</button>
            <button type="button" class="btn btn-primary ml-3" onclick="clearAll(true)">清除</button>
        </div>
        
        <!-- <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-default">下注金额</span>
            </div>
            <input type="text" id="inputGroupSelect01" value="1" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
        </div> -->
    <div>

    <button type="button" class="btn btn-primary" onclick="change(1)">选号</button>
    <button type="button" class="btn btn-info" onclick="change(2)">单双</button>
</div>

<script>
    var status = true;
    var open_result = true;
    var last_period_enable = true;
    var selectPlayType = 1;
    var total_amount = 0;
    $(function() {
        getLastPeriod();
        if(st_) getStatus();
        st_ = false;
    });

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
                        $('#time').text("计时 "+ d.time);
                        refresh(d);
                        if(last_period_enable)
                            getLastPeriod();
                        status = true;
                        open_result = true;
                    }
                    else if(d.status == "stop")
                    {
                        $('#time').text("结算中 "+ d.time);
                        status = false;
                        runOpen();
                    }
                }
                else
                {
                    $('#time').text("下一盘 "+ d.time);
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
            let sd = (e % 2 == 0) ?"雙":"單";
            html += '<p> 第 ' + (index + 1) + ' 回合 獎號： ' + sd + ' </p>';
        });
         
        $('#round').append(html);
    }

    function update_last_period(period) {
        $('#history').html('');
        var html = '<p> 上一期 期數：' + period.pid;
        html += '<p> ' + ' 終極密碼：' + period.open + '</p>';
        period.round.forEach(function(e, index){
            let sd = (e % 2 == 0) ?"雙":"單";
            html += '<p> 第 ' + (index + 1) + ' 回合 獎號： ' + sd + ' </p>';
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
            alert('余额不足');
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
                        alert("下注成功");
                    }
                    else
                    {
                        if(msg.code == 6)
                            alert("下注失败: 請物頻繁下注");
                        else    
                            alert("下注失败: " + msg.message);
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
                            if(e['type'] == 1)
                            {
                                alert("下注号码 " + e['num'] + " 中奖金额" + e['payout']);
                            }
                            else
                            {
                                var text = "单";
                                if(e['num'] == 0) text = "双";
                                alert("下注单双 " + text + " 中奖金额" + e['payout']);
                            }
                            
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
        var msg = '確認' + message;
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
            alert('余额不足');
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

        if (selectPlayType == 1)
        {
            for (var i = 1; i <= 40; i++) {
                if ($('#b' + i).hasClass("selected")){
                    data.push(i);
                }
            }
        }
        else
        {
            if($('#single').hasClass("selected")) data.push('s');
            if($('#double').hasClass("selected")) data.push('d');
        }

        if (doubleCheck("下注"))
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
                        alert("下注成功");
                        clearAll(false);
                    }
                    else
                    {
                        if(msg.code == 6)
                            alert("下注失败: 請物頻繁下注");
                        else    
                            alert("下注失败: " + msg.message);
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