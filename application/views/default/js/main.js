$(document).ready(function() {
	var server = {host: "78.90.51.149", port: "9095"};
	var muted = false; var muted_count = 0;
	var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	
	var Command = {
		MESSAGE_STATUS: 0,
		MESSAGE: 1,
		CHANGE_NAME: 2,
		PING: 3,
		KILL: 4,
		MUTE: 5,
		CONSOLE_MESSAGE: 6,
		NULL: 255
	};
	
	Modernizr.load({
		test: Modernizr.websockets,
		nope: base_url + 'application/views/default/js/web-socket-js/web_socket.js'
	});
	
	var SocketClient = {};
	
	function Connect() {
		showMask();
		
		maskedMsg('Connecting...');
		// If we're using the Flash fallback, we need Flash.
		if (!window.WebSocket && !swfobject.hasFlashPlayerVersion('10.0.0')) {
			alert('Flash Player >= 10.0.0 is required.');
			return;
		}

		SocketClient = new Socket({
			Server: server.host,
			Port: server.port,
		});

		SocketClient.Connected = function() {
			var Data = { comm: Command.CHANGE_NAME, data: { name: user.username, id: user.id }};
			
			//jBeep(base_url + 'application/views/default/sounds/connected.wav');
			maskedMsg('Successfuly Connected...');
			setTimeout(function() {
				setTimeout(function() {
					hideMask();
				}, 1000);
				socketSend(Data);
			}, 1000);
		};
		
		SocketClient.Disconnected = function() {
			if(muted_count >= 3) {
				window.onbeforeunload = function() {};
				window.location.replace(base_url);
				return;
			}
			showMask();
			setTimeout(Connect, 3000);
			
			maskedMsg('Connecting...');
		};

		SocketClient.MessageReceived = function(msg) {
			msgRecieved(msg);
		};
		
		SocketClient.Start();
	};
	
	function maskedMsg(msg) {
		if($('#mask').length > 0) {		
			$('#status').html(msg);
		}
	}
	
	function msgSend() {
		var msg = $('#write_message').val().trim();
		if(muted) {
			alert('You are muted for ' + muted + ' seconds!');
			return;
		}
		if( ! msg) {
			alert('Please enter your message !');
			return;
		}
		
		var Data = { comm: Command.MESSAGE, data: { name: user.username, id: user.id, message: msg.replace(/(<([^>]+)>)/ig,"")}};
		socketSend(Data);
		$('#write_message').val('');
		$('#write_message').focus();
	}
	
	function socketSend(msg) {
		if( ! msg)
			return;
				
		SocketClient.Send(msg);
	}
	
	function msgRecieved(Data) {
		switch(Data.comm) {
			case Command.PING:
				socketSend(Data);
				break;
			case Command.KILL:
				window.onbeforeunload = function() {};
				window.location.replace(base_url);
				break;
			case Command.MUTE:
				$('#send_message, #write_message').attr('disabled', 'disabled');
				muted = Data.data;
				muted_count++;
				$("#messages").append('<p><strong>You are mutted for ' + muted + ' seconds.</strong></p>');
				setTimeout(function(){
					$('#send_message, #write_message').removeAttr('disabled');
					muted = false;
				}, Data.data * 1000);
				break;
			case Command.MESSAGE:
				$("#messages").append('<p><strong>' + Data.data.name.replace(/(<([^>]+)>)/ig,"") + '</strong> said: <i>' + Data.data.message.replace(/(<([^>]+)>)/ig,"").replace(exp,"<a href='$1' target='blank'>$1</a>").replace(/(\r\n|\n|\r)/gm,"<br />") + '</i></p>');
				$("#messages").animate({ scrollTop: $('#messages')[0].scrollHeight}, 200);
				/*if(Data.data.name != user.username)
					jBeep(base_url + 'application/views/default/sounds/message.wav');*/
				break;
			case Command.CONSOLE_MESSAGE:
				$("#messages").append('<p>' + Data.data.replace(/(<([^>]+)>)/ig,"").replace(exp,"<a href='$1' target='blank'>$1</a>").replace(/(\r\n|\n|\r)/gm,"<br />") + '</p>');
				$("#messages").animate({ scrollTop: $('#messages')[0].scrollHeight}, 200);
				/*if(Data.data.name != user.username)
					jBeep(base_url + 'application/views/default/sounds/message.wav');*/
				break;
		}
	}
	
	function socketClose() {
		if( ! SocketClient)
			return;
		
		SocketClient.Stop();
		SocketClient = {};
	}

	Connect();
	
	$('#write_message').keydown(function(e) {
		if (e.keyCode == 13 && !e.shiftKey) {
			msgSend();
			return false;
		}
	});
	
	$('#send_message').click(function(e) {
		msgSend();
	});
/*
	$('#write_message').typing({
		start: function (event, $elem) {
			var Data = { comm: Command.MESSAGE_STATUS, data: {id: user.id, type: 1}};
			SocketClient.Send(Data);
		},
		stop: function (event, $elem) {
			var Data = { comm: Command.MESSAGE_STATUS, data: {id: user.id, type: 0}};
			SocketClient.Send(Data);
		},
		delay: 400
	});*/
	
	function showMask() {
		if($('#mask').length > 0) return;
			
		$('.body').append('<div id="mask"><img id="loader" style="margin-top: 5px;" src="' + base_url + 'application/views/default/img/loader.gif" /><p id="status"></p></div>');
		$('#mask').fadeIn(100);
	}
		
	function hideMask() {
		if($('#mask').length <= 0) return;
			
		$('#mask').fadeOut(200 , function() { 
			$('#mask, #loader, #status').remove(); 
		});
	}
});

String.prototype.trim = function () {
	return this.replace(/^\s+|\s+$/g,'');
};

window.onbeforeunload = function() {
    return 'Are you sure you want to navigate away from this page?';
};