<div>
    <div>
        期数
        <span id="pid"><?php echo $period; ?></span>
        <span id="time" style="position:absolute;right:200px">倒数 <?php echo $time % 60; ?></span>
        <div>
        <p>赔率 </p>
            <span>终极密码<span id="n"><?php echo $rate->n; ?></span></span>&nbsp;
            <span>单<span id="s"><?php echo $rate->s; ?></span></span>&nbsp;
            <span>双<span id="d"><?php echo $rate->d; ?></span></span>&nbsp;
        </div>
        
    </div>
    <div class="ball">
        <?php
        for ($i = 1; $i <= $total; $i++) {
            echo '<div class="circle" id="b' . $i . '"><a href="#" onClick="send(' . $i . ')"><span>' . $i . '</span></a></div>';
        }
        ?>
    </div>
    <div class="single">
        <div class="circle" id="s"><a href="#" onClick="send('s')"><span>单</span></a></div>
        <div class="circle" id="d"><a href="#" onClick="send('d')"><span>双</span></a></div>
    </div>
</div>
<div class="se">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <label class="input-group-text" for="inputGroupSelect01">下注金额</label>
        </div>
        <select class="custom-select" id="inputGroupSelect01">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
        </select>
    </div>
    <!-- <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-default">下注金额</span>
        </div>
        <input type="text" id="inputGroupSelect01" value="1" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
    </div> -->

    <button type="button" class="btn btn-primary" onclick="change(0)">选号</button>
    <button type="button" class="btn btn-info" onclick="change(1)">单双</button>
</div>

<script>
    var status = true;
    var open_result = true;
    $(function() {
        let max = <?php echo $max; ?>;
        let min = <?php echo $min; ?>;
        enable_ball(min, max);
        if(st_) getStatus();
        st_ = false;
    });

    function enable_ball(min, max) {
        for (var i = 1; i <= 40; i++) {
            if (min <= i && i <= max) {
                $('#b' + i).removeClass("disablediv");
                $('#b' + i).removeClass("winPwd");
            } else {
                $('#b' + i).addClass("disablediv");
            }
        }
    }

    function change(cmd) {

        if (cmd == 0) {
            $('.single').hide();
            $('.ball').show();
        } else {
            $('.single').show();
            $('.ball').hide();
        }
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
                    if (d.time <= 60)
                    {
                        if(d.time < 5)
                        {
                            $('#pid').text(d.pid);
                            enable_ball(d.min, d.max);
                            $('#n').text(d.rate.n);
                            $('#s').text(d.rate.s);
                            $('#d').text(d.rate.d);
                        }
                        $('#time').text("计时 "+ (60 - d.time));
                        status = true;
                        open_result = true;
                    }
                    else if(d.time > 60)
                    {
                        $('#time').text("结算中 "+ (10 - (d.time % 60)));
                        status = false;
                        runOpen();
                    }
                }
                else
                {
                    $('#time').text("下一盘 "+ (10 - (d.time % 60)));
                    $('#b' + d.pwd).addClass("winPwd");
                    runOpen();
                }
                
            }
        });

    }

    function runOpen()
    {
        if(open_result)
        {
            open_result = false;
            getResult();
        }
    }

    function send(number) {
        // console.log(number);
        let type = 1;
        if(isNaN(number)) type = 2;
        let amount = $('#inputGroupSelect01').val();
        if(checkAmount(amount)) 
        {
            alert('余额不足');
            return;
        }
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
                    alert("下注失败: " + msg.message);
                }
                console.log(msg);
            }
        })
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
</script>