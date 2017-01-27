<?php
/*
Plugin Name: Git Link
Description:
Version: 1
Author: oxzin.com
Author URI: http://oxzin.com
*/
// function to create the DB / Options / Defaults					
function ss_options_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "git_link";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (            
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`link` VARCHAR(255) NOT NULL DEFAULT '0',
	`name` VARCHAR(255) NOT NULL DEFAULT '0',
	`body` TEXT NULL,
	`last_tag_version` VARCHAR(255) NOT NULL DEFAULT '0',
	`username` VARCHAR(255) NOT NULL DEFAULT '0',
	`repository` VARCHAR(255) NOT NULL DEFAULT '0',
	`last_modified` DATETIME NULL DEFAULT NULL,
	`added_date` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'ss_options_install');

//menu items
add_action('admin_menu','git_modifymenu');
function git_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Gits Link', //page title
	'Gits Link', //menu title
	'manage_options', //capabilities
	'gits_link_list', //menu slug
	'gits_link_list' //function
	);
	
	//this is a submenu
	add_submenu_page('gits_link_list', //parent slug
	'Add New Link', //page title
	'Add New', //menu title
	'manage_options', //capability
	'git_link_create', //menu slug
	'git_link_create'); //function
	
	
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Link', //page title
	'Update', //menu title
	'manage_options', //capability
	'git_link_update', //menu slug
	'git_link_update'); //function
	
	
	add_submenu_page("gits_link_list", //parent slug
	'Update Release', //page title
	'Update Release', //menu title
	'manage_options', //capability
	'get_links_api', //menu slug
	'get_links_api'); //function
	
	
}

register_activation_hook(__FILE__, 'activateScheduler');

function activateScheduler() {
    if (! wp_next_scheduled ( 'updateRelease' )) {
	wp_schedule_event(time(), 'hourly', 'updateRelease');
    }
}

add_action('updateRelease', 'do_this_hourly');

function do_this_hourly() {
	get_links_api();
}

register_deactivation_hook(__FILE__, 'deactivateScheduler');

function deactivateScheduler() {
	wp_clear_scheduled_hook('updateRelease');
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'git-list.php');
require_once(ROOTDIR . 'git-create.php');
require_once(ROOTDIR . 'git-update.php');
require_once(ROOTDIR . 'git-post.php');

