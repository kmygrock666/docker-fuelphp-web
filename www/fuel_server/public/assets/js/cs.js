var conn;
function connect(url, userdata){
    return new Promise(resolve => {
        conn = new ab.Session(url,
            function() {
                console.log('ConnectionWampServer established!');
                send_ws('user', userdata);
                resolve();
            },
            function() {
                console.warn('WebSocketWampServer connection closed');
            },
            {'skipSubprotocolCheck': true}
        );
    });

}

function subscribeTopic(topic, callback)
{
    conn.subscribe(topic, callback);
}

function send_ws(topic, data)
{
    conn.publish(topic, data);
}