<div>
    <div>
        期数
        <span id="pid"><?php echo $period; ?></span>
        <span id="time" style="position:absolute;right:200px">倒数 <?php echo $time % 60; ?></span>
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
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>
    </div>

    <button type="button" class="btn btn-primary" onclick="change(0)">选号</button>
    <button type="button" class="btn btn-info" onclick="change(1)">单双</button>
</div>

<script>
    var status = true;
    $(function() {
        let max = <?php echo $max; ?>;
        let min = <?php echo $min; ?>;
        enable_ball(min, max);
        getStatus();
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
                        }
                        $('#time').text("计时 "+ (60 - d.time));
                        status = true;
                    }
                    else if(d.time > 60)
                    {
                        $('#time').text("结算中 "+ (10 - (d.time % 60)));
                        status = false;
                    }
                }
                else
                {
                    $('#time').text("下一盘 "+ (10 - (d.time % 60)));
                    $('#b' + d.pwd).addClass("winPwd");
                }
                
            }
        });

    }

    function send(number) {
        // console.log(number);
        let type = 1;
        if(isNaN(number)) type = 2;
        let amount = $('#inputGroupSelect01').val();
        $.ajax({
            url: "api/bet/send",
            type: 'get',
            dataType: "json",
            data:{'b':number, 't': type, m: amount},
            success: function (msg) {
                if(msg.code == 0)
                {
                    // resfreshBalance(money);
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
</script>