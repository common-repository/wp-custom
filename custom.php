<?php
/*
Plugin Name: WP-Custom
Plugin URI: http://kovshenin.com/wordpress/plugins/wp-custom/
Description: Easy posts customization
Author: Konstantin Kovshenin
Version: 0.2.2
Author URI: http://kovshenin.com/

*/

function wpcustom_init() {
	add_action("admin_menu", "wpcustom_config_menus");
	add_action("admin_menu", "wpcustom_box");
	add_action("save_post", "wpcustom_save");
}

add_action("init", "wpcustom_init");

function wpcustom_config_menus() {
	add_menu_page("WP-Custom Configuration", "WP-Custom", 8, "wp-custom/manage.php", "wpcustom_config_manage");
	//add_submenu_page("wp-custom/config.php", "WP-Custom Configuration", "General", 8, "wp-custom/config.php", "wpcustom_config_general");
	add_submenu_page("wp-custom/manage.php", "WP-Custom Management", "Manage", 8, "wp-custom/manage.php", "wpcustom_config_manage");
 	add_submenu_page("wp-custom/manage.php", "WP-Custom Support", "Support", 8, "wp-custom/support.php", "wpcustom_config_support");
}

function wpcustom_config_general() {
	require("custom-config-general.php");
}

function wpcustom_config_manage() {
	require("custom-config-manage.php");
}

function wpcustom_config_support() {
	require("custom-config-support.php");
}

function wpcustom_box($post_id) {
	if (function_exists("add_meta_box")) {
		add_meta_box("wpcustom_id", "Custom", "wpcustom_inner_box", "post", "normal");
		add_meta_box("wpcustom_id", "Custom", "wpcustom_inner_box", "page", "normal");
	}
	else {
		add_action("dbx_post_advanced", "wpcustom_old_box");
		add_action("dbx_page_advanced", "wpcustom_old_box");
	}
}

function wpcustom_old_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="wpcustom_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
	      "Custom" . "</h3></div>";   
	echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
	wpcustom_inner_box();
	echo "</div></div></fieldset></div>\n";
}

function wpcustom_inner_box($post) {
	$options = get_option("wp-custom-data");
	$post_id = $post->ID;
	foreach ($options as $key => $value)
	{
		$uid = $key;
		$name = $value["name"];
		$type = $value["type"];
		$default = $value["default"];
		
		$value = get_post_meta($post_id, $uid, true);
		if (!$value) $value = $default;
		
		if ($type == "text")
			$field_html = "<input class=\"widefat\" id=\"wpcustom-".$uid."\" name=\"wpcustom-".$uid."\" type=\"text\" value=\"".$value."\" />";
		elseif ($type == "textarea")
			$field_html = "<textarea class=\"widefat\" id=\"wpcustom-".$uid."\" name=\"wpcustom-".$uid."\">".$value."</textarea>";
		elseif ($type == "url")
		{
			$attachments = wpcustom_get_attachments();
			$field_html = "<input class=\"widefat\" id=\"wpcustom-".$uid."\" name=\"wpcustom-".$uid."\" type=\"text\" value=\"".$value."\" />";
			$field_html .= '<a href="#" onclick="document.getElementById(\'wpcustom-url-'.$uid.'\').style.display = \'block\'; return false;">Select URL from attached files</a>';
			$field_html .= '<div id="wpcustom-url-'.$uid.'" style="display: none"><ul style="list-style: disc; margin-left: 30px;">';
			if (!empty($attachments))
				foreach ($attachments as $file)
				{
					$field_html .= '<li style="font-size: 10px;"><a href="#" onclick="document.getElementById(\'wpcustom-'.$uid.'\').value = \''.$file["url"].'\'; return false;">'.$file["title"].'</a></li>';
				}
			$field_html .= '</ul></div>';
		}
?>
	<p><label for="wpcustom-<?=$uid;?>"><?=$name; ?> <?=$field_html;?></label></p>
<?
	}
	echo '<input type="hidden" id="wpcustom-submit" name="wpcustom-submit" value="1" />';
}

// Whenever the user saves a post or page
function wpcustom_save($post_id) {
	if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;

	$options = get_option("wp-custom-data");

	foreach ($options as $key => $value)
	{
		if (isset($_POST["wpcustom-$key"]))
		{
			$value = $_POST["wpcustom-$key"];
		
			if ($options[$key]["options"]["filterhtml"] == "checked")
				$value = strip_tags($value);
			
			update_post_meta($post_id, $key, $value) or add_post_meta($post_id, $key, $value, true);
		}
	}
}

// WP-Custom service functions
// Get a list of attachments to the current post
function wpcustom_get_attachments() {
	global $wpdb, $post;
	$attachment_str = '';
	$query = "SELECT `guid`, `post_content`, `post_title` FROM {$wpdb->posts} WHERE (post_status = 'attachment' || post_type = 'attachment') AND post_parent = '{$post->ID}'";
	$attachments = $wpdb->get_results($query);
	if (count($attachments) > 0) {
		$attachment_list = array();
		foreach ( $attachments as $attachment ) {
			$attachment_list[] = array("title" => $attachment->post_title, "url" => $attachment->guid);
		}
	}
	return $attachment_list;
}


// For use in themes. Parameters are the unique id of a defined field and whether to return the results or echo them (echo by default)
function wpcustom($field_name, $return = false) {
	global $post;
	$custom_field = get_post_meta($post->ID, $field_name, true);
	if ($return) return $custom_field;
	else echo $custom_field;
}