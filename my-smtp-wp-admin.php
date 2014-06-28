<?php
function my_smtp_wp_admin(){
	add_options_page('My SMTP WP Options', 'My SMTP WP','manage_options', __FILE__, 'my_smtp_wp_page');
}

function my_smtp_wp_page(){
	$ws_nonce = wp_create_nonce('my_ws_nonce');
	global $wsOptions;
	if(isset($_POST['my_smtp_mail_update']) && isset($_POST['my_smtp_mail_nonce_update'])){
		if(!wp_verify_nonce(trim($_POST['my_smtp_mail_nonce_update']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$wsOptions = array();
		$wsOptions["from"] = trim($_POST['my_smtp_mail_from']);
		$wsOptions["fromname"] = trim($_POST['my_smtp_mail_fromname']);
		$wsOptions["host"] = trim($_POST['my_smtp_mail_host']);
		$wsOptions["smtpsecure"] = trim($_POST['my_smtp_mail_smtpsecure']);
		$wsOptions["port"] = trim($_POST['my_smtp_mail_port']);
		$wsOptions["smtpauth"] = trim($_POST['my_smtp_mail_smtpauth']);
		$wsOptions["username"] = trim($_POST['my_smtp_mail_username']);
		$wsOptions["password"] = trim($_POST['my_smtp_mail_password']);
		$wsOptions["deactivate"] = (isset($_POST['my_smtp_mail_deactivate'])) ? trim($_POST['my_smtp_mail_deactivate']) : "";
		update_option("my_smtp_wp_options",$wsOptions);
		if(!is_email($wsOptions["from"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"From\" must be a valid email address!","my-smtp-mail") . '</strong></p></div>';
		}
		elseif(empty($wsOptions["host"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"SMTP Host\" can not be left blank!","my-smtp-mail") . '</strong></p></div>';
		}
		else{
			echo '<div id="message" class="updated fade"><p><strong>' . __("Options saved.","my-smtp-mail") . '</strong></p></div>';
		}
	}
	if(isset($_POST['my_smtp_mail_test']) && isset($_POST['my_smtp_mail_nonce_test'])){
		if(!wp_verify_nonce(trim($_POST['my_smtp_mail_nonce_test']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$to = trim($_POST['my_smtp_mail_to']);
		$subject = trim($_POST['my_smtp_mail_subject']);
		$message = trim($_POST['my_smtp_mail_message']);
		$failed = 0;
		if(!empty($to) && !empty($subject) && !empty($message)){
			try{
				$result = wp_mail($to,$subject,$message);
			}catch(phpmailerException $e){
				$failed = 1;
			}
		}
		else{
			$failed = 2;
		}
		if(!$failed){
			if($result==TRUE){
				echo '<div id="message" class="updated fade"><p><strong>' . __("Message sent!","my-smtp-mail") . '</strong></p></div>';
			}
			else{
				$failed = 1;
			}
		}
		if($failed == 1){
			echo '<div id="message" class="updated fade"><p><strong>' . __("Some errors occurred!","my-smtp-mail") . '</strong></p></div>';
		}
		elseif($failed == 2){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The fields \"To\" \"Subject\" \"Message\" can not be left blank when testing!","my-smtp-mail") . '</strong></p></div>';
		}
	}
?>
<div class="wrap">
	
<?php screen_icon(); ?>
<h2>
My SMTP WP
</h2>
<form action="" method="post" enctype="multipart/form-data" name="my_smtp_wp_form">

<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php _e('From','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_from" value="<?php echo $wsOptions["from"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('From Name','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_fromname" value="<?php echo $wsOptions["fromname"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Host','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_host" value="<?php echo $wsOptions["host"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Secure','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input name="my_smtp_mail_smtpsecure" type="radio" value=""<?php if ($wsOptions["smtpsecure"] == '') { ?> checked="checked"<?php } ?> />
				None
			</label>
			&nbsp;
			<label>
				<input name="my_smtp_mail_smtpsecure" type="radio" value="ssl"<?php if ($wsOptions["smtpsecure"] == 'ssl') { ?> checked="checked"<?php } ?> />
				SSL
			</label>
			&nbsp;
			<label>
				<input name="my_smtp_mail_smtpsecure" type="radio" value="tls"<?php if ($wsOptions["smtpsecure"] == 'tls') { ?> checked="checked"<?php } ?> />
				TLS
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Port','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_port" value="<?php echo $wsOptions["port"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('SMTP Authentication','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input name="my_smtp_mail_smtpauth" type="radio" value="no"<?php if ($wsOptions["smtpauth"] == 'no') { ?> checked="checked"<?php } ?> />
				No
			</label>
			&nbsp;
			<label>
				<input name="my_smtp_mail_smtpauth" type="radio" value="yes"<?php if ($wsOptions["smtpauth"] == 'yes') { ?> checked="checked"<?php } ?> />
				Yes
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Username','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_username" value="<?php echo $wsOptions["username"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Password','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="password" name="my_smtp_mail_password" value="<?php echo $wsOptions["password"]; ?>" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Delete Options','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="checkbox" name="my_smtp_mail_deactivate" value="yes" <?php if($wsOptions["deactivate"]=='yes') echo 'checked="checked"'; ?> />
				<?php _e('Delete options while deactivate this plugin.','my-smtp-mail'); ?>
			</label>
		</td>
	</tr>
</table>

<p class="submit">
<input type="hidden" name="my_smtp_mail_update" value="update" />
<input type="hidden" name="my_smtp_mail_nonce_update" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
</p>

</form>

<form action="" method="post" enctype="multipart/form-data" name="my_smtp_mail_testform">
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php _e('To:','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_to" value="" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Subject:','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_subject" value="" size="43" style="width:272px;height:24px;" />
			</label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('Message:','my-smtp-mail'); ?>
		</th>
		<td>
			<label>
				<textarea type="text" name="my_smtp_mail_message" value="" cols="45" rows="3" style="width:284px;height:62px;"></textarea>
			</label>
		</td>
	</tr>
</table>
<p class="submit">
<input type="hidden" name="my_smtp_mail_test" value="test" />
<input type="hidden" name="my_smtp_mail_nonce_test" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" value="<?php _e('Send Test','my-smtp-mail'); ?>" />
</p>
</form>

</div>
<?php 
}

?>