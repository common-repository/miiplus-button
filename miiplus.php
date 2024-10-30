<?php
/*
    Plugin Name: MiiPlus Button
    Plugin URI: http://www.miiplus.com
    Description: MiiPlus allows you to add plus buttons to your website.
    Version: 1.0
    Author: Peter Law
    Author URL: http://www.miiplus.com/

    Copyright 2011 Peter Law (peterlaw108@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

class MiiplusButton {
	var $hook 		= 'miiplus';
	var $filename	= 'miiplus/miiplus.php';
	var $longname	= 'MiiPlus Configuration';
	var $shortname	= 'MiiPlus Configuration';
	
	public $plugin_url = 'miiplus/';
	
	var $sizes = array("mini"=>"15", "small"=>20, "normal"=>"24", "tall"=>"60");
	var $mode = array("read"=>"Read", "user"=>"User", "love"=>"Love", "hate"=>"Hate");
	var $theme = array("default"=>"Default", "black"=>"Black", "blue"=>"Blue", "green"=>"Green", "grey"=>"Grey", "red"=>"Red", "white"=>"White", "yellow"=>"Yellow");
	var $position = array("before"=>"Before post", "after"=>"After post");
	var $align = array("left"=>"Left", "right"=>"Right");
	
	function MiiplusButton() {
		$this->__construct();
	}
	
	function __construct() {
		global $wpdb, $post;
		register_activation_hook( __FILE__, array(&$this, 'install_miiplus') );
		add_action('admin_menu', array(&$this, 'register_admin_page'));	
		add_filter('plugin_action_links', 	array(&$this, 'add_action_link'), 10, 2);
		add_action('add_meta_boxes', array(&$this, 'custom_boxes'));
		add_action('draft_post', array(&$this, 'save_post_options'));
		add_action('publish_post', array(&$this, 'save_post_options'));
		add_action('save_post', array(&$this, 'save_post_options'));
		if (!is_home() && !is_admin()) {
			wp_enqueue_style('miiplus', get_bloginfo('wpurl').'/wp-content/plugins/'.$this->plugin_url.'css/style.css');
			add_filter('the_content', array(&$this, 'the_content'));
		}
	}
	function install_miiplus() {
		update_option("size", "normal");	
		update_option("mode", "user");
		update_option("theme", "default");
		update_option("position", "before");
		update_option("align", "left");
		update_option("showpage", "1");
		update_option("showpost", "1");
	}
	function register_admin_page() {
		wp_enqueue_style('admin', get_bloginfo('wpurl').'/wp-content/plugins/'.$this->plugin_url.'css/admin.css');
		add_options_page($this->longname, $this->shortname, 'edit_user', $this->hook, array(&$this, 'config_page'));
		
		# Include javascript on the GA settings page
		//add_action('admin_head-' . $plugin_page, 'ga_admin_ajax');
	}
	function config_page() {
		
		
		if (isset($_POST['info_update'])) {
			update_option("size", $_POST["size"]);	
			update_option("mode", $_POST["mode"]);
			update_option("theme", $_POST["theme"]);
			update_option("position", $_POST["position"]);
			update_option("align", $_POST["align"]);
			update_option("showpage", $_POST["showpage"]);
			update_option("showpost", $_POST["showpost"]);
		}
		
		echo '<div class="wrap">';
		echo '	<div class="header">';
		echo '		<div class="logo"></div>';
		echo '		<h2>'.$this->longname.'</h2><div class="clearfix"></div>';
		echo '	</div>';
		if (isset($_POST['info_update'])) {
			echo '				<div class="message-container">';
			echo '					<span>Update successfully</span>';
			echo '				</div>';
		}
		echo '	<div class="postbox-container" style="width:65%;">';
		echo '		<div class="metabox-holder">';
		echo '			<div id="miiplus-setting" class="postbox">';
		echo '				<div class="handlediv" title="Click to toggle"><br></div>';
		echo '				<h3 class="hndle"><span>MiiPlus Setting</span></h3>';
		echo '				<div class="inside">';
		echo '					<form method="post" action="options-general.php?page=miiplus.php">';
		echo '					<table class="form-table">';
		echo '					<tr>';
		echo '						<td>Size:</td>';
		echo '						<td>';
		foreach ($this->sizes as $key=>$value) {
			if ( get_option("size") == $key) {
				$checked = "checked=checked";
			} else {
				$checked = "";
			}	
			echo '						<span class="option"><input name="size" id="size" type="radio" value="'.$key.'" '.$checked.' />' . $value . '</span>';
		}
		echo '						</td>';
		echo '					</tr>';
		echo '					<tr>';
		echo '						<td>Mode:</td>';
		echo '						<td>';
		foreach ($this->mode as $key=>$value) {
			if ( get_option("mode") == $key) {
				$checked = "checked=checked";
			} else {
				$checked = "";
			}	
			echo '						<span class="option"><input name="mode" id="mode" type="radio" value="'.$key.'" '.$checked.' />' . $value . '</span>';
		}
		echo '						</td>';
		echo '					</tr>'; 
		echo '					<tr>';
		echo '						<td>Color:</td>';
		echo '						<td>';
		foreach ($this->theme as $key=>$value) {
			if ( get_option("theme") == $key) {
				$checked = "checked=checked";
			} else {
				$checked = "";
			}	
			echo '						<span class="option"><input name="theme" id="theme" type="radio" value="'.$key.'" '.$checked.' />' . $value . '</span>';
		}
		echo '						</td>';
		echo '					</tr>'; 
		echo '					<tr>';
		echo '						<td>Position:</td>';
		echo '						<td>';
		foreach ($this->position as $key=>$value) {
			if ( get_option("position") == $key) {
				$checked = "checked=checked";
			} else {
				$checked = "";
			}	
			echo '						<span class="option"><input name="position" id="position" type="radio" value="'.$key.'" '.$checked.' />' . $value . '</span>';
		}
		echo '						</td>';
		echo '					</tr>'; 
		echo '					<tr>';
		echo '						<td>Align:</td>';
		echo '						<td>';
		foreach ($this->align as $key=>$value) {
			if ( get_option("align") == $key) {
				$checked = "checked=checked";
			} else {
				$checked = "";
			}	
			echo '						<span class="option"><input name="align" id="align" type="radio" value="'.$key.'" '.$checked.' />' . $value . '</span>';
		}
		echo '						</td>';
		echo '					</tr>'; 
		echo '					<tr>';
		echo '						<td>Show:</td>';
		echo '						<td>';
		if ( get_option("showpost") == "1") {
			$checked = "checked=checked";
		} else {
			$checked = "";
		}
		echo '							<span class="option"><input name="showpost" id="showpost" type="checkbox" value="1" '.$checked.' />Show on post</span>';
		if ( get_option("showpage") == "1") {
			$checked = "checked=checked";
		} else {
			$checked = "";
		}
		echo '							<span class="option"><input name="showpage" id="showpage" type="checkbox" value="1" '.$checked.' />Show on page</span>';
		echo '						</td>';
		echo '					</tr>'; 
		
		echo '					</table>';
		echo '					<p class="submit"><input type="submit" name="info_update" value="Submit" /></p>';
		echo '					</form>';
		echo '				</div>';
		echo '			</div>';
		echo '		</div>';
		echo '	</div>';
		echo '</div>';
	}

	function custom_boxes() {
		add_meta_box( 'MiiPlus', 'MiiPlus Settings',  array(&$this, 'post_options'), 'post', 'side', 'low');
	}
	
	function post_options(){
		global $post;
		$miiplus_hide = get_post_meta($post->ID, 'miiplus_hide', true);
		if ($miiplus_hide) {
			$checked = "checked=checked";
		} else {
			$checked = "";
		}
		echo '<p>';
		echo '<input name="miiplus_hide" id="miiplus_hide" type="checkbox" '.$checked.' />';
		echo '<label for="miiplus_hide">Disable on this post?</label>';
		echo '</p>';
	}
	
	function save_post_options($post_id) {
		if (!isset($_POST['miiplus_hide']) || empty($_POST['miiplus_hide'])) {
			delete_post_meta($post_id, 'miiplus_hide');
			return;
		}
		$post = get_post($post_id);
		if (!$post || $post->post_type == 'revision') return;
		update_post_meta($post_id, 'miiplus_hide', true);
	}

	function plugin_options_url() {
		return admin_url( 'options-general.php?page='.$this->hook );
	}
	
	function add_action_link( $links, $file ) {
		static $this_plugin;
		if( empty($this_plugin) ) $this_plugin = $this->filename;
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
	
		
	function the_content($content) {
		$str = '';
		$miiplus_hide = get_post_meta($post->ID, 'miiplus_hide', true);
		if(empty($miiplus_hide)) {
			$str .= '<div class="miiplus-container '.get_option("align").'">';
			$str .= '<mii:plus theme="'.get_option("theme").'" mode="'.get_option("mode").'" size="'.get_option("size").'"></mii:plus>';
			$str .= '<script language="javascript" src="http://www.miiplus.com/button/js/miiplus.js"></script>';
			$str .= '</div>';
		}
		$targetstr = '';
		if (get_option("position") == "before") {
			$targetstr = $str . $content;
		} elseif (get_option("position") == "after") {
			$targetstr = $content . $str;
		}
		
		if (is_page()) {
			if (get_option("showpage") == "1") {
				echo $targetstr;
			} else {
				echo $content;
			}
		}
		if (is_single()) {
			if (get_option("showpost") == "1") {
				echo $targetstr;
			} else {
				echo $content;
			}
		}
		
		
	}
	
}

$miiplusButton = new MiiplusButton();


?>