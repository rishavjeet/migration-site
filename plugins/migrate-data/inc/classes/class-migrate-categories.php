<?php
/**
 * For migrating categories data
 *
 * @package migrate-data
 */

namespace Migrate\Data\Inc;

use \Migrate\Data\Inc\Traits\Singleton;


class Migrate_Categories {


	use Singleton;

	/**
	 * Constructor function
	 */
	public function __construct()
	{
		
	}

	/**
	  * Migrates category data from the 'categories' table to WordPress categories.
	  *
	  * This function retrieves a specified number of categories per page from the 'categories' table,
	  * transforms the data into WordPress category format, and inserts it into the WordPress database.
	  *
	  * @param array $args        An array of arguments for the data migration.
	  * @param array $args_assoc  An associative array containing specific parameters for the migration.
	  *                            - 'items_per_page' (int) Number of categories to migrate per page. Default is 10.
	  *                            - 'page_number'    (int) The page number of categories to migrate. Default is 1.
	  *
	  * @return void
	  */
	public function category_data_migration( $args, $args_assoc ) {

		$db_obj = Plugin::get_instance();
		$conn = $db_obj->conn;

		$items_per_page = isset($args_assoc['items_per_page']) ? intval($args_assoc['items_per_page']) : 10;
    	$page_number = isset($args_assoc['page_number']) ? intval($args_assoc['page_number']) : 1;

		$offset = ($page_number - 1) * $items_per_page;

		$log_file = MIGRATE_DATA_PLUGIN_PATH . '/logs/category_migration_log.txt';
    	$log_handle = fopen( $log_file, 'a' );
		
		if ( $log_handle === false ) {
			\WP_CLI::error( 'Unable to open or create the log file.' );
			return;
		}
		
		$sql = "SELECT * FROM `categories` LIMIT $items_per_page OFFSET $offset";
		$result = $conn->query( $sql );
		while ( $row = $result->fetch_assoc() ) {

			$term_data = array(
				'alias_of' => '',
				'description' => '',
				'parent' => '',
				'slug' => $row['slug']
			);

			$term_id = wp_insert_term( 
				$row['name'],
				'category',
				$term_data
			);

			$meta_id = add_term_meta( $term_id, 'prev_term_id', $row['id'] );

			if( ! ( is_wp_error( $term_id ) &&  is_wp_error( $meta_id ) && ! $meta_id ) ) {
				\WP_CLI::success( $row['name'] . 'Category inserted successfully' );
				fwrite( $log_handle, $row['name'] . ' Category added successfully: ' . PHP_EOL );
			} else {
				\WP_CLI::success( 'Action Failed' );
				fwrite( $log_handle, ' Action Failed ! ' );
			}
		}

	}

}

\WP_CLI::add_command('migrate_categories', 'Migrate\Data\Inc\Migrate_Categories');
