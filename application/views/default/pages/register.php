<div class="navigation"><div class="logo">Регистрация</div></div>
<div class="navigation_bottom"></div>
<div class="navigation_top"></div>
<div class="wrapper_content">
	<div class="login_left">
		<form action="<?php echo $base_url; ?>index.php/home/register_validation" method="post">
			<?php if($register_status == 2) echo 'Регистрацията премина успешно.<br />Вече можете да <a style="text-decoration: underline;" href="'.$base_url.'index.php/home/index/login">влезнете</a> в акаунта си!<div class="clear" style="height: 15px;"></div>'; ?>
			<div class="clear"></div>
			<div class="left" style="padding-top: 7px;padding-left: 2px;">
				<label for="register_username">Потребителско име:</label>
			</div>
			<div class="right">
				<input type="text" id="register_username" name="register_username" autocomplete="off" <?php if(in_array($register_status, array(-1, -2))) echo 'class="warning"';  if($register_status == -2) echo 'title="<font style=\'color: red;\'>Потребителското име вече е заето!</font><br />Потребителското име трябва да е с дъжина между 3 и 40 символа."'; else echo 'title="Потребителското име трябва да е с дъжина между 3 и 40 символа."' ?> placeholder="Username *" /> 
			</div>
			<div class="clear" style="height: 5px;"></div>
			<div class="left" style="padding-top: 7px;padding-left: 2px;">
				<label for="register_password">Парола:</label> 
			</div>
			<div class="right">
				<input type="password" id="register_password" title="Паролата трябва да е с дъжина между 6 и 40 символа.<br />Не използвайте очевидни пароли и не ги споделяйте с никого !" name="register_password" <?php if(in_array($register_status, array(-1))) echo 'class="warning"'; ?> placeholder="Password *" /> 
			</div>
			<div class="clear" style="height: 5px;"></div>
			<div class="left" style="padding-top: 7px;padding-left: 2px;">
				<label for="register_password_confirm">Повторете паролата:</label> 
			</div>
			<div class="right">
				<input type="password" id="register_password_confirm" title="Повторете паролата." name="register_password_confirm" <?php if(in_array($register_status, array(-1))) echo 'class="warning"'; ?> placeholder="Password Confirm *" /> 
			</div>
			<div class="clear" style="height: 5px;"></div>
			<div class="left" style="padding-top: 7px;padding-left: 2px;">
				<label for="register_email">Имейл:</label> 
			</div>
			<div class="right" >
				<input type="text" id="register_email" autocomplete="off" <?php if(in_array($register_status, array(-1, -3))) echo 'class="warning"';  if($register_status == -3) echo 'title="<font style=\'color: red;\'>Имейла вече е използван!</font><br />Моля въведете валиден имейл.<br />Можете да използвате букви, цифри и точки."'; else echo 'title="Моля въведете валиден имейл.<br />Можете да използвате букви, цифри и точки."' ?> name="register_email" placeholder="Email *" /> 
			</div>
			<div class="clear" style="height: 5px;"></div>
			<div class="left" style="padding-top: 7px;">
				<label for="register_captcha" style="padding-left: 1px;">Код за потвърждение:</label><br/>
				<input type="text" id="register_captcha" title="Въведете кода който виждатe на картинката" style="width: 200px;" autocomplete="off" <?php if(in_array($register_status, array(-1, -4))) echo 'class="warning"'; ?> name="register_captcha" placeholder="Captcha *" />
			</div>
			<div class="right" style="margin-top: 27px;">
				<?php echo $captcha['image']; ?> 
			</div>
			<div class="clear" style="height: 15px;"></div>
			<div class="left" style="padding-top: 7px;">
				Вече имате акаунт? <a href="<?php echo $base_url; ?>index.php/home/index/login">Вход</a>
			</div>
			<div class="right">
				<input type="submit" name="register_submit" value="Register"/> 
			</div>
			
			<div class="clear" style="height: 5px;"></div>
		</form><div class="clear"></div>
	</div>
	<div class="login_right">
		Може да използвате своята регистрция от <strong>facebook</strong> или <strong>twitter</strong> за да се регистрирате в Alc0.
		<div id="fb_login_button">Регистрация от <strong>facebook</strong></div>
		<div id="twitter_login_button">Регистрация от <strong>twitter</strong></div>
		<div id="fb-root"></div>
	</div>
	<div class="clear"></div>
	<script>
		$(function() {
			$( "input:text, input:password" ).tooltip({ 
				position: { my: "left+15 bottom-5", at: "right top" }, 
				tooltipClass: "custom_tooltip",
				content: function () {
					return $(this).prop('title');
				},
				disabled: true
			}).on("focusin", function () {
				$(this).tooltip("enable").tooltip("open");
			}).on("focusout", function () {
				$(this).tooltip("close").tooltip("disable");
			});
			
			$("#login_username").focus();
			$("#fb_login_button").click(function() {
				FB.login(function(response) {
					if (response.authResponse) {
						window.location = '<?php echo $base_url; ?>' + 'index.php/home/login_validation_fb/0';
					} 
				}, {scope: 'email'});
			});
			window.fbAsyncInit = function() {
				FB.init({
					appId: '194753930687466',
					cookie: true,
					xfbml: true,
					oauth: true
				});
			};
		});
		
		(function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
		}());
	</script>
</div>