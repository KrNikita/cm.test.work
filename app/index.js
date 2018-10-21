function get(url,success_cb,error_cb) {
    var request = new XMLHttpRequest();
    request.open('GET', url, true);

    request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
            success_cb(request.responseText);
        } else {
            error_cb(request.responseText);
        }
    };

    request.onerror = function() {
        error_cb('load error');
    };

    request.send();
}

function postJSON(url,data,success_cb,error_cb){
    var request = new XMLHttpRequest();
    request.open('POST', url, true);
    request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
    request.send(data);
    request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
            success_cb(request.responseText);
        } else {
            error_cb(request.responseText);
        }
    };

    request.onerror = function() {
        error_cb('load error');
    };
}

function UpdateMessagesList(){
    postJSON('/requestProcessing.php',
            JSON.stringify({
                action:'get_messages_list'
            }),
            function(data){
                document.getElementById('result_container_id').innerHTML = data;
            },
            function(error){
                alert(error);
            });
}

function loadMessagesList(btn_elm){
    var _username = document.getElementById('username_id').value;
    var _password = document.getElementById('password_id').value;
    btn_elm.setAttribute('disabled','true');
    postJSON('/requestProcessing.php',
        JSON.stringify({
            action:'get_mails_thru_imap',
            username:_username,
            password:_password
        }),
        function(data){
            console.log(data);
            document.getElementById('result_container_id').innerHTML = data;
            btn_elm.removeAttribute('disabled');
        },
        function(error){
            alert(error);
            btn_elm.removeAttribute('disabled');
        });
}

function loadMessagesFromIMAPMicrosoftServer(user,pass,btn_elm){
    btn_elm.setAttribute('disabled','true');
    postJSON('/requestProcessing.php',
        JSON.stringify({
            action:'get_mails_thru_imap',
            username:user,
            password:pass
        }),
        function(data){
            console.log(data);
            btn_elm.removeAttribute('disabled');
        },
        function(error){
            alert(error);
            btn_elm.removeAttribute('disabled');
        })
}

function loadMessagesViaMsGraphAPI(access_token,btn_elm){
    btn_elm.setAttribute('disabled','true');
    postJSON('/requestProcessing.php',
            JSON.stringify({
                action:'load_messages_ms_graph_api',
                accessToken:access_token
            }),
            function(data){
                console.log(data);
                btn_elm.removeAttribute('disabled');
            },
            function(error){
                alert(error);
                btn_elm.removeAttribute('disabled');
            });
}