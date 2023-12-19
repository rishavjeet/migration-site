<?php
/**
 * Trait to implement the singleton Design pattern which ensures single instantiation of a class.
 * 
 * @package migrate-data
 */

namespace Migrate\Data\Inc\Traits;

/**
 * Trait Singleton
 *
 * The Singleton trait provides a common implementation of the singleton pattern,
 * ensuring that a class has only one instance and providing a global point of access to it.
 */
trait Singleton {

	/**
      * Protected constructor to prevent creating instances of the Singleton trait directly.
      */
	protected function __construct() {

	}

	/**
      * Final method to prevent cloning instances of the Singleton trait.
      */
	final protected function __clone() {

	}

	/**
      * Get the singleton instance of the class.
      *
      * @return static The singleton instance of the class.
      */
	final public static function get_instance() {
		
		static $instance = [];

		$called_class = get_called_class();

		if( ! isset( $instance[ $called_class ] ) ) {
			$instance[ $called_class ] = new $called_class();
		}

		return $instance[ $called_class ];
	}
}
