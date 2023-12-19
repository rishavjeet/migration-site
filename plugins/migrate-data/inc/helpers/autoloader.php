<?php
/**
 * Autoloser for the plugin
 * 
 * @package migrate-data
 */

namespace Migrate\Data\Inc\Helpers;

function autoloader( $resource='' ) {
	
	$resource_path = false;
	$namespace_root = 'Migrate\Data\\';
	$resource = trim( $resource, '\\' );

	if( empty($resource) ||  false === strpos( $resource, '\\' ) || 0  !== strpos( $resource, $namespace_root ) ) {
		return;
	}

	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource ))
	);

	if( empty( $path[0] ) || empty($path[1]) ) {
		return;
	}


	$directory = '';
	$file_name = '';
	if( 'inc' === $path[0] ) {
		switch( $path[1] ){
			case 'traits': 
				$directory = 'traits';
				$file_name = sprintf( 'trait-%s', trim( strtolower( $path[2] ) ) );
				break;
			default :
				$directory = 'classes';
				$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
				break;
		}

		$resource_path = sprintf( '%s/inc/%s/%s.php', MIGRATE_DATA_PLUGIN_PATH, $directory, $file_name );
	}


	$resource_path_valid = validate_file( $resource_path );

	if( ! empty( $resource_path ) && file_exists( $resource_path ) && ( 0 === $resource_path_valid || 2 === $resource_path_valid ) ) {
		require_once( $resource_path );
	}

}

spl_autoload_register( 'Migrate\Data\Inc\Helpers\autoloader' );
