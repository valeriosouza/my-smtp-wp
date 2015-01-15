<?php
function my_smtp_wp_admin(){
	add_options_page('My SMTP WP Options', 'My SMTP WP','manage_options', __FILE__, 'my_smtp_wp_page');
	add_action('load-my_smtp_wp_admin', 'my_smtp_wp_help_tab');
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
		$wsOptions["replyto"] = trim($_POST['my_smtp_reply_to']);
		$wsOptions["host"] = trim($_POST['my_smtp_mail_host']);
		$wsOptions["smtpsecure"] = trim($_POST['my_smtp_mail_smtpsecure']);
		$wsOptions["port"] = trim($_POST['my_smtp_mail_port']);
		$wsOptions["smtpauth"] = trim($_POST['my_smtp_mail_smtpauth']);
		$wsOptions["username"] = trim($_POST['my_smtp_mail_username']);
		$wsOptions["password"] = trim($_POST['my_smtp_mail_password']);
		$wsOptions["returnpath"] = trim($_POST['my_smtp_mail_returnpath']);
		$wsOptions["deactivate"] = (isset($_POST['my_smtp_mail_deactivate'])) ? trim($_POST['my_smtp_mail_deactivate']) : "";
		update_option("my_smtp_wp_options",$wsOptions);
		if(!is_email($wsOptions["from"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"From Email\" must be a valid email address!","my-smtp-mail") . '</strong></p></div>';
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
				echo '<div id="message" class="updated fade"><p><strong>' . __("Message sent successfully!","my-smtp-mail") . '</strong></p></div>';
			}
			else{
				$failed = 1;
			}
		}
		if($failed == 1){
			echo '<div id="message" class="updated fade"><p><strong>' . __("Some errors occurred! Check the settings!","my-smtp-mail") . '</strong></p></div>';
		}
		elseif($failed == 2){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The fields \"To\" can not be left blank when testing!","my-smtp-mail") . '</strong></p></div>';
		}
	}
	if(is_admin()){require_once('my-smtp-wp-admin.php');}
}
function my_smtp_wp_help_tab () {
    $screen = get_current_screen();

    // Add my_help_tab if current screen is My Admin Page
    $screen->add_help_tab( array(
        'id'	=> 'my_help_tab',
        'title'	=> __('My Help Tab'),
        'content'	=> '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
    ) );
}
?>
