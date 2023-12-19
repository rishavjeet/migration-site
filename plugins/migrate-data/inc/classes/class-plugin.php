<?php
/**
 * The Main Plugin class
 * 
 * @package migrate-data
 */

namespace Migrate\Data\Inc;

use \Migrate\Data\Inc\Traits\Singleton;

class Plugin {

	use Singleton;

	public $conn;

	/**
	 * Constructor function
	 */
	public function __construct() {

		$this->connect_migrate_db();

	}

	public function connect_migrate_db() {
		$this->conn = new \mysqli( MIGRATE_DB_HOST, MIGRATE_DB_USER, MIGRATE_DB_PASSWORD, MIGRATE_DB_NAME );

		if( $this->conn->connect_error ) {
			var_dump( 'SQL Connection failed' );
			wp_die();
		}
		return;
	}
}
