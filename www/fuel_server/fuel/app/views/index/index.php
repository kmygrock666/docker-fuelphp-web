<div class="starter-template">
    <h1>Weclome to the ultimate password game</h1>
    <p class="lead">
        <?php
            echo $direction;
        ?>
        <!-- <br> 
        All you get is this text and a mostly barebones HTML document. -->
    </p>
    <div class="input-group mb-3" style="margin-left: 38%">
        <form id="save_form" onsubmit="return completeAndRedirect('save_form')">
        <?php
            echo $power;
        ?>
        </form>
    </div>
</div>
<input type="button" id="button" value="Send" onclick="send();" />
<input type="button" id="button" value="Connect" onclick="connect();" />
<script>
    function completeAndRedirect(form_id){
        // console.log(form_id);
        // console.log($('#' + form_id).serialize());
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
                    resfreshBalance(data.data.after)
                }
                else
                {
                    alert(data.message);
                }
            }
        });
        return false;
    }
    var conn;
    function connect(){
        conn = new WebSocket('ws://localhost');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
        };
    }

    function send() {
        var message = document.getElementById("text").value;
        conn.send(message);
    }
</script>