<?php
/**
 * Plugin Name: Migrate Data
 * Author: Rishav
 * Description: This is a plugin to migrate data to Wordpress
 * Version: 1.0.0
 */

define( 'MIGRATE_DATA_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MIGRATE_DATA_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'MIGRATE_DATA_PLUGIN_FILE', __FILE__ ); 

define( 'MIGRATE_DB_HOST', 'kwr.h.filess.io:3307' );
define( 'MIGRATE_DB_NAME', 'migrationdb_breathing' );
define( 'MIGRATE_DB_USER', 'migrationdb_breathing' );
define( 'MIGRATE_DB_PASSWORD', '76a9ecf995a997ee2e2beb505975001f15090694');



require_once MIGRATE_DATA_PLUGIN_PATH . '/inc/helpers/autoloader.php';
require_once MIGRATE_DATA_PLUGIN_PATH . '/inc/classes/class-plugin.php';

if( defined('WP_CLI') && WP_CLI ){
	require_once MIGRATE_DATA_PLUGIN_PATH . '/inc/classes/class-migrate-user.php';
	require_once MIGRATE_DATA_PLUGIN_PATH . '/inc/classes/class-migrate-categories.php';
	require_once MIGRATE_DATA_PLUGIN_PATH . '/inc/classes/class-migrate-articles.php';
}

function migrate_data_plugin_db_connect(){
	\Migrate\Data\Inc\Plugin::get_instance();
}

migrate_data_plugin_db_connect();