<div>
    <div>
        <?php echo Lang::get('games.PERIOD'); ?>
        <span id="pid"></span>
        <span id="time" style="position:absolute;right:200px">倒数 </span>
        <div>
        <p><?php echo Lang::get('games.RATIO'); ?> </p>
            <span><?php echo Lang::get('games.ULTIMATE_PASSWORD'); ?> <span id="n"></span></span>&nbsp;
            <span><?php echo Lang::get('games.SINGLE'); ?> <span id="s"></span></span>&nbsp;
            <span><?php echo Lang::get('games.DOUBLE'); ?> <span id="d"></span></span>&nbsp;
        </div>
        <div id="round" style="float:left">

        </div>
        <div id="history" style="float:right"></div>
    </div>
    <div class="ball">
        <?php
        for ($i = 1; $i <= $total; $i++) {
            echo '<div class="circle" id="b' . $i . '"><a href="#" onClick="selected(' . $i . ')"><span>' . $i . '</span></a></div>';
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
    var selectPlayType = 1;
    var total_amount = 0;
    var lang = <?php echo $lang; ?>;
    openModal();
    // closeModal();
    $("#loadingModal").modal('toggle');
    $(function() {

        if(st_) {
            wsConnect(<?php echo $userid; ?>);
        } else {
            send_ws('history', '[]');
        }
        st_ = false;
    });

    async function wsConnect(id){
        await connect('ws://10.0.2.15:8080', '{"gt": "up","userId": ' + id + '}');
        subscribeTopic("bet", bet_callback);
        subscribeTopic("period", period_callback);
        subscribeTopic("history", history_callback);
        subscribeTopic("winner", winner_callback);
        send_ws('history', '[]');
    }

    function bet_callback(topic, req){
        // console.log("bet_callback : " + topic + " / " + req.title);
        console.log(req);
        if(req.code == 0)
        {
            var object = req.data;
            resfreshBalance(object.amount);
            alert(req.message);
            clearAll(false);
        }
        else
        {
            alert(req.message);
            clearAll(true);
        }
    }

    function period_callback(topic, req){
        // console.log("period_callback : " + topic + " / " + req.title);
        setStatus(req.data);
        console.log(req.data);
    }

    function history_callback(topic, req){
        // console.log("history_callback : " + topic + " / " + req.title);
        console.log(req);
        update_last_period(req.data);

    }

    function winner_callback(topic, req){
        // console.log("winner_callback : " + topic + " / " + req.title);
        // console.log(req);
        showResult(req);
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

    function setStatus(d) {

        if(d.close == false) {
            if (d.status == "bet") {
                refresh(d);
                $('#time').text(lang.time+ " " + d.time);
                status = true;
                open_result = true;
            } else if (d.status == "stop") {
                $('#time').text(lang.settle + " " + d.time);
                status = false;
            }
        } else {
            $('#time').text(lang.next + " " + d.time);
            $('#b' + d.pwd).addClass("winPwd");
        }
    }

    function refresh(d){
        if($('#pid').text() == "" || $('#round>p').length != d.round_number.length)
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
            let sd = (e % 2 == 0) ? lang.double : lang.single;
            html += '<p>' + (index + 1) + lang.round_arawd + '： '+ e + "/" + sd + ' </p>';
        });
         
        $('#round').append(html);
    }

    function update_last_period(period) {
        $('#history').html('');
        var html = '<p>'+ lang.previous + ' : ' + period.pid;
        html += '<p> ' + lang.ultimate_password +' ：' + period.open + '</p>';
        period.round.forEach(function(e, index){
            let sd = (e % 2 == 0) ? lang.double : lang.single;
            html += '<p>' + (index + 1) + lang.round_arawd + '： '+ e + "/" + sd + ' </p>';
        });
        
        $('#history').append(html);
    }

    function showResult(msg) {
        var e = msg.data;
        console.log(e)
        if (e != '[]') {
            // console.log("showResult")
            addBalance(e['payout']);
            var text = e['num'];
            if (e['type'] == 2) {
                if (e['num'] == 0) text = lang.double;
                else text = lang.single;
            }
            alert(lang.bet_data + ": " + text + " ," + lang.bet_data + ": " + e['payout']);
        }
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
        let win = '';
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

            if (win != '') {
                win = win.substring(0, win.lastIndexOf("/"));
            }

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
            let bd = {
                "b": data,
                "t": selectPlayType,
                "m": amount
            }
            send_ws('bet', JSON.stringify(bd));
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