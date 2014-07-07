<?php
/*
Plugin Name: My SMTP WP
Plugin URI: https://github.com/valeriosouza/my-smtp-wp
Description: WP SMTP can help us to send emails via SMTP instead of the PHP mail() function.
Version: 1.0.0
Author: Valerio Souza
Author URI: http://valeriosouza.com.br
Text Domain: my-smtp-mail
Domain Path: /languages
*/

// Actions and Filters

add_filter('init','load_my_smtp_wp_lang');

add_action('phpmailer_init','my_smtp_wp');

register_activation_hook( __FILE__ , 'my_smtp_wp_activate' );

add_filter('plugin_action_links','my_smtp_wp_settings_link',10,2);

add_action('admin_menu', 'my_smtp_wp_admin');

$wsOptions = get_option("my_smtp_wp_options");

if($wsOptions["deactivate"]=="yes"){
	register_deactivation_hook( __FILE__ , create_function('','delete_option("my_smtp_wp_options");') );
}

// Functions

function load_my_smtp_wp_lang(){
	$currentLocale = get_locale();
	if(!empty($currentLocale)){
		$moFile = dirname(__FILE__) . "/languages/my-smtp-wp-" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) { load_textdomain('my-smtp-mail',$moFile); }
	}
}

function my_smtp_wp($phpmailer){
	global $wsOptions;
	if( !is_email($wsOptions["from"]) || empty($wsOptions["host"]) ){
		return;
	}
	$phpmailer->Mailer = "smtp";
	$phpmailer->From = $wsOptions["from"];
	$phpmailer->FromName = $wsOptions["fromname"];
	$phpmailer->Sender = $phpmailer->From; //Return-Path
	$phpmailer->AddReplyTo($phpmailer->From,$phpmailer->FromName); //Reply-To
	$phpmailer->Host = $wsOptions["host"];
	$phpmailer->SMTPSecure = $wsOptions["smtpsecure"];
	$phpmailer->Port = $wsOptions["port"];
	$phpmailer->SMTPAuth = ($wsOptions["smtpauth"]=="yes") ? TRUE : FALSE;
	if($phpmailer->SMTPAuth){
		$phpmailer->Username = $wsOptions["username"];
		$phpmailer->Password = $wsOptions["password"];
	}
}

function my_smtp_wp_activate(){
	$wsOptions = array();
	$wsOptions["from"] = "";
	$wsOptions["fromname"] = "";
	$wsOptions["host"] = "";
	$wsOptions["smtpsecure"] = "";
	$wsOptions["port"] = "";
	$wsOptions["smtpauth"] = "yes";
	$wsOptions["username"] = "";
	$wsOptions["password"] = "";
	$wsOptions["deactivate"] = "";
	add_option("my_smtp_wp_options",$wsOptions);
}

function my_smtp_wp_settings_link($action_links,$plugin_file){
	if($plugin_file==plugin_basename(__FILE__)){
		$ws_settings_link = '<a href="options-general.php?page=' . dirname(plugin_basename(__FILE__)) . '/class-my-smtp-wp.php">' . __("Settings") . '</a>';
		array_unshift($action_links,$ws_settings_link);
	}
	return $action_links;
}

if(is_admin()){require_once('class-my-smtp-wp.php');}

?>