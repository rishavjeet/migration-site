<?php
/**
 * For migrating categories data
 * 
 * @package migrate-data
 */

namespace Migrate\Data\Inc;

use \Migrate\Data\Inc\Traits\Singleton;


class Migrate_Articles {


	use Singleton;

	/**
	 * Constructor function
	 */
	public function __construct()
	{
		
	}

	/**
	  * Migrates article data from the 'articles' table to WordPress posts.
	  *
	  * This function retrieves a specified number of articles per page from the 'articles' table,
	  * transforms the data into WordPress post format, and inserts it into the WordPress database.
	  *
	  * @param array $args        An array of arguments for the data migration.
	  * @param array $args_assoc  An associative array containing specific parameters for the migration.
	  *                            - 'items_per_page' (int) Number of articles to migrate per page. Default is 10.
	  *                            - 'page_number'    (int) The page number of articles to migrate. Default is 1.
	  *
	  * @return void
	  */
	public function article_data_migration( $args, $args_assoc ) {

		$db_obj = Plugin::get_instance();
		$conn = $db_obj->conn;

		$items_per_page = isset($args_assoc['items_per_page']) ? intval($args_assoc['items_per_page']) : 10;
    	$page_number = isset($args_assoc['page_number']) ? intval($args_assoc['page_number']) : 1;

		$offset = ($page_number - 1) * $items_per_page;

		$log_file = MIGRATE_DATA_PLUGIN_PATH . '/logs/article_migration_log.txt';
    	$log_handle = fopen( $log_file, 'a' ); 

		if ( $log_handle === false ) {
			\WP_CLI::error( 'Unable to open or create the log file.' );
			return;
		}
		
		$sql = "SELECT * FROM `articles` LIMIT $items_per_page OFFSET $offset";
		$result = $conn->query( $sql );
		while ( $row = $result->fetch_assoc() ) {
			$post_arr = array(
				'post_author' => $row['author'],
				'post_date'   => $row['added'],
				'post_date_gmt' => $row['added'],
				'post_content' => $row['html'],
				'post_content_filtered' => '',
				'post_title' => $row['title'],
				'post_excerpt' => '',
				'post_status' => 'draft',
				'post_type' => 'post',
				'comment_status' => get_option( 'default_comment_status' ),
				'ping_status' => get_option( 'default_ping_status' ),
				'post_password' => '',
				'post_name' => $row['title'],
				'to_ping' => '',
				'pinged' => '',
				'post_parent' => 0,
				'menu_order' => 0,
				'post_mime_type' => '',
				'guid' => '',
				'import_id' => 0,
				'post_category' => [ $row['category'] ],
				'tags_input' => [],
				'tax_input' => [],
				'meta_input' => [],
				'page_template' => ''
			);

			$post_id = wp_insert_post( $post_arr );

			$meta_id = add_post_meta( $post_id, 'old_post_id', $row['id'] );

			if( ! ( is_wp_error( $post_id ) || is_wp_error( $meta_id ) ) ) {
				\WP_CLI::success( $row['title'] . 'Post inserted successfully' );
				fwrite( $log_handle, $row['title'] . ' Post added successfully: ' . PHP_EOL );
			} else {
				\WP_CLI::success( 'Post insertion Failed' );
				fwrite( $log_handle, 'Post insertion Failed' . PHP_EOL );
			}
		}
		fclose( $log_handle );

	}

}

\WP_CLI::add_command('migrate_articles', 'Migrate\Data\Inc\Migrate_Articles');
