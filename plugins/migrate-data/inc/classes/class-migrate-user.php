<?php
/**
 * For migrating user data
 * 
 * @package migrate-data
 */

namespace Migrate\Data\Inc;

use \Migrate\Data\Inc\Traits\Singleton;
use WP_Error;

class Migrate_User {


	use Singleton;

	/**
	 * Constructor function
	 */
	public function __construct()
	{
		
	}

	/**
	  * Migrates user data from the 'users' table to WordPress user accounts.
	  *
	  * This function retrieves a specified number of users per page from the 'users' table,
	  * transforms the data into WordPress user account format, and inserts it into the WordPress database.
	  *
	  * @param array $args        An array of arguments for the data migration.
	  * @param array $args_assoc  An associative array containing specific parameters for the migration.
	  *                            - 'items_per_page' (int) Number of users to migrate per page. Default is 10.
	  *                            - 'page_number'    (int) The page number of users to migrate. Default is 1.
	  *
	  * @return void
	  */
	public function user_data_migration( $args, $args_assoc ) {


		$db_obj = Plugin::get_instance();
		$conn = $db_obj->conn;

		$items_per_page = isset($args_assoc['items_per_page']) ? intval($args_assoc['items_per_page']) : 10;
    	$page_number = isset($args_assoc['page_number']) ? intval($args_assoc['page_number']) : 1;

		$offset = ($page_number - 1) * $items_per_page;

		$log_file = MIGRATE_DATA_PLUGIN_PATH . '/logs/user_migration_log.txt';
    	$log_handle = fopen( $log_file, 'a' ); 

		if ( $log_handle === false ) {
			\WP_CLI::error( 'Unable to open or create the log file.' );
			return;
		}

		
		$sql = "SELECT * FROM `users` LIMIT $items_per_page OFFSET $offset";
		$result = $conn->query( $sql );
		while ( $row = $result->fetch_assoc() ) {

			$user_role = $row['role'] !== 'admin' ? $row['role'] : 'administrator';

			$user_data = array(
				'username' => $row['first_name'],
				'user_pass' => $row['passoword'],
				'user_login' => $row['first_name'],
				'user_nicename' => $row['first_name'],
				'user_url' => $row['email'],
				'user_email' => $row['email'],
				'display_name' => $row['first_name'],
				'nickname' => $row['first_name'],
				'first_name' => $row['first_name'],
				'last_name' => $row['last_name'],
				'description' => '',
				'rich_editing' => 'true',
				'syntax_highlighting' => 'true',
				'comment_shortcuts' => 'false',
				'admin_color' => 'fresh',
				'use_ssl' => false,
				'user_registered' => 'Y-m-d H:i:s',
				'user_activation_key' => '',
				'spam' => false,
				'show_admin_bar_front' => 'true',
				'role' => $user_role,
				'locale' => '',
				'meta_input' => ''
			);
	
			$user_id = wp_insert_user( $user_data );

			$meta_id = add_user_meta( $user_id, 'old_id', $row['id'] );
			 
			if( ! is_wp_error( $user_id ) && $meta_id ) {
				\WP_CLI::success('User added successfully: ' . $user_id );
				fwrite( $log_handle, $row['first_name'] . ' User added successfully: ' . $user_id . PHP_EOL );
			} else {
				\WP_CLI::error( 'Action Failed' );
				fwrite( $log_handle, 'Action Failed File Action' . PHP_EOL );
			}

		}
		fclose( $log_handle );

	}

}

\WP_CLI::add_command('migrate_user', 'Migrate\Data\Inc\Migrate_User');
