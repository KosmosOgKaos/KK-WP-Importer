<?php
/*
 Plugin Name: KK WP Importer
 Plugin URI: http://kosmosogkaos.is
 Description: KK WP importer
 Version: 1.0
 Author: KosmosogKaos
 Text Domain: kkwpimporter
 */

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'kk-wp-importer-options.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'kk-wp-importer-class.php' );
}
