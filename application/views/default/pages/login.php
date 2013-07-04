<div class="navigation"><div class="logo">Вход</div></div><div class="navigation_bottom"></div>
<div class="navigation_top"></div>
<div class="wrapper_content">
	<div class="login_left">
		<form action="<?php echo $base_url; ?>index.php/home/login_validation" method="post">
			<div class="clear"></div>
			<div class="left" style="padding-top: 7px;">
				<label for="login_username">Потребителско име</label>
			</div>
			<div class="clear"></div>
			<div class="left">
				<input type="text" id="login_username" <?php if(in_array($login_status, array(-1))) echo 'class="warning"'; ?> name="login_username" placeholder="Потребителско име *" /> 
			</div>
			<div class="clear" style="height: 15px;"></div>
			<div class="left" style="padding-top: 7px;">
				<label for="login_password">Парола</label> 
			</div>
			<div class="clear"></div>
			<div class="left">
				<input type="password" id="login_password" <?php if(in_array($login_status, array(-1))) echo 'class="warning"'; ?> name="login_password" placeholder="Парола *"/> 
			</div>
			<?php if($login_samples && $login_samples > 3) { ?>
				<div class="clear" style="height: 5px;"></div>
				<div class="left" style="padding-top: 7px;">
					<label for="login_captcha" style="padding-left: 1px;">Код за потвърждение:</label><br/>
					<input type="text" id="login_captcha" title="Въведете кода който виждатe на картинката" style="width: 200px;" autocomplete="off" <?php if(in_array($login_status, array(-1, -2))) echo 'class="warning"'; ?> name="login_captcha" placeholder="Captcha *" />
				</div>
				<div class="right" style="margin-top: 27px;">
					<?php echo $captcha['image']; ?> 
				</div>
			<?php } ?>
			<div class="clear" style="height: 15px;"></div>
			<div class="left" style="padding-top: 6px">
				<input type="checkbox" style="margin-left: 0px;" value="1" id="login_remember_me" name="login_remember_me" /><label for="login_remember_me" class="right" style="padding-top: 1px">Запомни ме</label>
			</div>
			<div class="right">
				<input type="submit" name="login_submit" value="Вход"/> 
			</div>
			<div class="clear" style="height: 10px"></div>
			<div class="left" style="padding-top: 7px;padding-right: 4px;">
				Забравили сте <a href="<?php echo $base_url; ?>index.php/home/index/register">паролата</a> си?
			</div>
			<div class="clear"></div>
			<div class="left" style="padding-top: 7px;padding-right: 4px;">
				Нямате регистрация? Регистрирайте се <a href="<?php echo $base_url; ?>index.php/home/index/register">тук</a>!
			</div>
			<div class="clear" style="height: 5px;"></div>
		</form>
	</div>
	<div class="login_right">
		<div id="fb_login_button">Вход чрез <strong>facebook</strong></strong></div>
		<div id="twitter_login_button">Вход чрез <strong>twitter</strong></div>
		<div id="fb-root"></div>
		<script>
			$(function() {
				$("#login_username").focus();
				$("#fb_login_button").click(function() {
					FB.login(function(response) {
						if (response.authResponse) {
							if(confirm("Искате ли да останете логнати?")) {
								window.location = '<?php echo $base_url; ?>' + 'index.php/home/login_validation_fb/1';
							} else {
								window.location = '<?php echo $base_url; ?>' + 'index.php/home/login_validation_fb/0';
							}
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
	<div class="clear"></div>
</div>