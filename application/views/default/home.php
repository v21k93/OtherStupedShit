<!DOCTYPE html>
<html lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="Ac0" />
        <meta name="description" content="Alc0" />
		<meta http-equiv="X-UA-Compatible" content="IE=10" >
        <title>Alc0</title>
        <link rel="stylesheet" href="<?php echo $base_url; ?>application/views/default/css/style.css">
		<!--[if lt IE 7]>
			<style type="text/css">
				.wrapper { height:100%; }
			</style>
		<![endif]-->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script src="<?php echo $base_url; ?>application/views/default/js/jquery-ui-tooltip.min.js"></script>
		<?php if($page == 'chat') { ?>
			<script>
				var base_url = "<?php echo $base_url; ?>";
				var user = {username: "<?php echo $username; ?>", id: "<?php echo $user_id; ?>" };
				
				WEB_SOCKET_SWF_LOCATION =  base_url + 'application/views/default/js/web-socket-js/WebSocketMain.swf';
				WEB_SOCKET_DEBUG = true;
				
				if (!window.WebSocket && !window.MozWebSocket) {
					var head = document.getElementsByTagName('head')[0];
					function include(filename) {
						var script = document.createElement('script');
						script.src = filename;
						script.type = 'text/javascript';

						head.appendChild(script)
					}
					include(base_url + "application/views/default/js/web-socket-js/swfobject.js");
					include(base_url + "application/views/default/js/web-socket-js/web_socket.js");
				}
			</script>
			<script src="<?php echo $base_url; ?>application/views/default/js/web-socket-js/modernizr-custom-2.0.min.js"></script>
			<script src="<?php echo $base_url; ?>application/views/default/js/socket.js"></script>
			<script src="<?php echo $base_url; ?>application/views/default/js/jBeep.min.js"></script>
			<script src="<?php echo $base_url; ?>application/views/default/js/json2.js"></script>
			<script src="<?php echo $base_url; ?>application/views/default/js/typing.js"></script>
			<script src="<?php echo $base_url; ?>application/views/default/js/main.js"></script>
		<?php } ?>
	</head>
    <body>
		<div class="wrapper">
			<div class="header">
				<div class="container">
					<div class="logo">Alc0</div>
					<div class="clear"></div>
					<div class="menu">
						<li><a href="<?php echo $base_url; ?>">Начало</a></li>
						<?php 
						if( ! $is_logged) 
							echo '<li><a href="'.$base_url.'index.php/home/index/register">Регистрация</a></li>';
						?>
						<li><a href="<?php echo $base_url; ?>index.php/home/index/downloads">Изтегляния</a></li>
						<?php 
						if($is_logged) 
							echo '<li class="last"><a href="'.$base_url.'index.php/home/log_out">Изход</a></li><li class="last" style="cursor: default;">Здравей, '.$username.' !</li>';
						else
							echo '<li class="last"><a href="'.$base_url.'index.php/home/index/login">Вход</a></li>';
						?>
						
					</div>
				</div>
			</div>
			<div class="body">
				<div class="container">
					<?php echo $content; ?>
				</div>
			</div>
			<div class="footer">
				<div class="container">
					<div class="left"><a href="<?php echo $base_url; ?>">&copy;Alc0&trade; 2013</a></div>
					<div class="right">Designed & Developed by <a href="<?php echo $base_url; ?>">Ac0&reg;</a></div>
				</div>
			</div>
		</div>
	</body>
</html>
