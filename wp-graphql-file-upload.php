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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( '\WPGraphQL\File_Upload' ) ) :

	final class File_Upload {

		/**
		 * Stores the instance of the File_Upload class
		 *
		 * @var File_Upload The one true File_Upload
		 * @since  0.0.1
		 * @access private
		 */
		private static $instance;

		/**
		 * The instance of the File_Upload object
		 *
		 * @return object|File_Upload - The one true File_Upload
		 * @since  0.0.1
		 * @access public
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof File_Upload ) ) {
				self::$instance = new File_Upload;
				self::$instance->setup_constants();
				self::$instance->includes();
			}

			self::$instance->init();

			/**
			 * Fire off init action
			 *
			 * @param File_Upload $instance The instance of the Init_File_Upload class
			 */
			do_action( 'graphql_file_upload_init', self::$instance );

			/**
			 * Return the Init_File_Upload Instance
			 */
			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function __clone() {

			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The Init_File_Upload class should not be cloned.', 'wp-graphql-file-upload' ), '0.0.1' );

		}

		/**
		 * Disable de-serializing of the class.
		 *
		 * @since  0.0.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {

			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL file upload class is not allowed.', 'wp-graphql-file-upload' ), '0.0.1' );

		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_VERSION' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_VERSION', '0.3.1' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_URL' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_FILE' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_FILE', __FILE__ );
			}

			// Whether to autoload the files or not
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_AUTOLOAD' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_AUTOLOAD', true );
			}

		}

		/**
		 * Include required files.
		 * Uses composer's autoload
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function includes() {

			// Autoload Required Classes
			if ( defined( 'WPGRAPHQL_FILE_UPLOAD_AUTOLOAD' ) && true === WPGRAPHQL_FILE_UPLOAD_AUTOLOAD ) {
				require_once( WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR . 'vendor/autoload.php' );
			}
		}

		/**
		 * Initialize the plugin
		 */
		private static function init() {
			/**
			 * Initialize the GraphQL fields for managing file uploads
			 */
			ManageUploads::init();
		}

	}

endif;

function init() {
	return File_Upload::instance();
}

add_action( 'graphql_init', '\WPGraphQL\File_Upload\init' );
