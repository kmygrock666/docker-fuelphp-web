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
<input type="button" id="button" value="ConnectIos" onclick="connect();" />
<input type="button" id="button" value="SendWamp" onclick="sendWamp();" />
<input type="button" id="button" value="ConnectWamp" onclick="connectWamp();" />
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
        conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
        };
    }

    function send() {
        var message = "123";
        conn.send(message);
    }
    var conn;
    function connectWamp(){
        conn = new ab.Session('ws://localhost:8080',
            function() {
                console.log('ConnectionWamp established!');
                conn.subscribe('history', function(topic, data) {
                    // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                    console.log('New article published to category "' + topic + '" : ' + data.title);
                    console.log(data);
                });
                conn.onConnect = function (e) {
                    console.log("ConnectionWamp established!");
                }
                senduser();
            },
            function() {
                console.warn('WebSocket connection closed');
            },
            {'skipSubprotocolCheck': true}
        );
    }
    function senduser(){
        var message = '{"gt": "up","userId": 2 }';
        conn.publish('user', message);
    }

    function sendWamp() {
        // conn.call('com.myapp.add2', [2, 3]).then(
        //     function (result) {
        //         console.log("Got result:", result);
        //     }
        // );
        // conn._send(message);
        conn.publish('history' , '[]');
    }

</script>