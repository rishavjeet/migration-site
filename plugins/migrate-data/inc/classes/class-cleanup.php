<?php
/**
 * Custom clean up command for legacy data.
 */

namespace Migrate\Data\Inc;

use \Migrate\Data\Inc\Traits\Singleton;

class Cleanup {
	 
	use Singleton;

	/**
	 * Constructor function
	 */
	public function __construct()
	{
		
	}

	public function cleanup_data() {

		$db_obj = Plugin::get_instance();
		$conn = $db_obj->conn;


		$sql_users = "DELETE from `users`";
		$sql_categories = "DELETE from `categories`";
		$sql_articles = "DELETE from `articles`";

		$res_users = $conn->query( $sql_users );
		$res_categories = $conn->query( $sql_categories );
		$res_articles = $conn->query( $sql_articles );

		if( $res_users && $res_categories && $res_articles ) {
			\WP_CLI::success( 'Legacy Data removed successfully !!' );
		} else {
			\WP_CLI::error( 'Something went wrong !' );
		}

	}

}

\WP_CLI::add_command('cleanup_cmd', 'Migrate\Data\Inc\Cleanup');