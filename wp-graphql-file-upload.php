<?php
/**
 * Plugin Name:     WPGraphQL File Upload
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Add the ability to upload files through GraphQL to your WPGraphQL schema.
 * Author:          Hughie Devore
 * Author URI:      hughdevore.com
 * Text Domain:     wp-graphql-file-upload
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WPGraphQL_File_Upload
 */
namespace WPGraphQL\File_Upload;
use ReactWPScripts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_File_Upload' ) ) :

	include_once( dirname( __FILE__ ) . '/wp-apollo-upload/react-wp-scripts.php' );

	class WP_File_Upload {

		public function init() {


			// Plugin Folder Path.
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
			}

			add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_react_app' ] );
		}

		public function is_wpgraphql_active() {
			return class_exists( 'WPGraphQL' );
		}

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		public function register_admin_page() {
			add_menu_page(
				__( 'WP Apollo Upload', 'wp-graphql-file-upload' ),
				'WP Apollo Upload',
				'manage_options',
				'wp-apollo-upload',
				[ $this, 'render_admin_page' ],
				plugin_dir_url( __FILE__ . '/wp-apollo-upload/src/cloud-computing.svg' ),
				1
			);
		}

		public function render_admin_page() {

			if ( $this->is_wpgraphql_active() ) {
				echo '<div class="wrap"><div id="root"></div></div>';
			} else {
				echo '<div class="wrap"><h1>This plugin requires WPGraphQL to be installed to work. Please install WPGraphQL (https://github.com/wp-graphql/wp-graphql) and visit this page again.</h1></div>';
			}

		}

		/**
		 * Enqueues the stylesheet and js for the WPGraphiQL app
		 */
		public function enqueue_react_app() {

			/**
			 * Only enqueue the assets on the proper admin page, and only if WPGraphQL is also active
			 */
			if ( strpos( get_current_screen()->id, 'wp-apollo-upload' ) && $this->is_wpgraphql_active() ) {
				ReactWPScripts\enqueue_assets( dirname( __FILE__ ) . '/wp-apollo-upload' );
			}

		}

	}

endif; // End if class_exists()

add_action(
	'plugins_loaded',
	function() {
		$wp_file_upload = new WP_File_Upload();
		$wp_file_upload->init();
	}
);
