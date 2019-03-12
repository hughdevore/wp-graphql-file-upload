<?php
/**
 * Plugin Name:     WPGraphQL File Upload
 * Plugin URI:      https://github.com/hughdevore/wp-graphql-file-upload
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

if ( ! class_exists( 'File_Upload' ) ) :

	/**
	 * Include react-wp-scripts so our React App loads
	 */
	include_once( dirname( __FILE__ ) . '/wp-apollo-upload/react-wp-scripts.php' );

	/**
	 * Class File_Upload
	 *
	 * This class adds the ability to upload local files to your WordPress site via the createMediaItem and
	 * updateMediaItem mutations.
	 *
	 * @package WPGraphQL\File_Upload
	 */
	class File_Upload {

		/**
		 * Init the Plugin
		 */
		public function init() {

			add_filter(
				'reactwpscripts.is_development',
				function() {
					return false;
				}
			);

			/**
			 * Define the Plugin Folder Path if it doesn't exist
			 */
			if ( ! defined( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR' ) ) {
				define( 'WPGRAPHQL_FILE_UPLOAD_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
			}

			/**
			 * Add the Upload type to WPGraphQL
			 */
			add_action( 'graphql_register_types', [ $this, 'add_upload_type' ] );

			/**
			 * Add the "File" field to the createMediaItem & updateMediaItem mutations
			 */
			add_action( 'init_graphql_request', [ $this, 'register_file_upload_field_on_media_item' ] );

			/**
			 * Add the admin menu tab and page
			 */
			add_action( 'admin_menu', [ $this, 'register_admin_page' ] );

			/**
			 * Enqueue the JS and styles for the React app
			 */
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_react_app' ] );

			/**
			 * Filter GraphQL request data to accept a file upload from multi-part form data
			 */
			//add_filter( 'graphql_request_data', [ 'WPGraphQL\File_Upload\ManageUploads', 'test_og_filter' ] );


			/**
			 * Test GraphQL Request filter data
			 */
			//add_filter( 'graphql_request_middleware', [ 'WPGraphQL\File_Upload\ManageUploads', 'test_new_filter' ] );

		}

		/**
		 * Check to see if WPGraphQL is active
		 * 
		 * @return bool
		 */
		public function is_wpgraphql_active() {
			return class_exists( 'WPGraphQL' );
		}

		/*
		 * Add a settings tab to the admin menu for "WP GraphQL File Upload" and the page containing
		 * the WP Apollo Upload React App for this plugin.
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

		/**
		 * Render the admin page for WP GraphQL File Upload
		 */
		public function render_admin_page() {

			if ( $this->is_wpgraphql_active() ) {
				echo '<div class="wrap"><div id="root"></div></div>';
			} else {
				echo '<div class="wrap"><h1>This plugin requires WPGraphQL to be installed to work. Please install WPGraphQL (https://github.com/wp-graphql/wp-graphql) and visit this page again.</h1></div>';
			}

		}

		/**
		 * Enqueues the stylesheet and js for the WP GraphQL File Upload app
		 */
		public function enqueue_react_app() {

			/**
			 * Only enqueue the assets on the proper admin page, and only if WPGraphQL is also active
			 */
			if ( strpos( get_current_screen()->id, 'wp-apollo-upload' ) && $this->is_wpgraphql_active() ) {
				ReactWPScripts\enqueue_assets(
					plugin_dir_path( __FILE__ ) . '/wp-apollo-upload',
					[
						'base_url' => '/wp-content/plugins/wp-graphql-file-upload/wp-apollo-upload',
						'handle'   => 'wp-apollo-upload',
					]
				);
			}

		}

		/**
		 * Register the upload type
		 */
		public function add_upload_type() {

			register_graphql_type(
				'Upload',
				[
					'kind'        => 'input',
					'description' => 'The `Upload` special type represents a file to be uploaded in the same HTTP request as specified by [graphql-multipart-request-spec](https://github.com/jaydenseric/graphql-multipart-request-spec).',
					'fields'      => [
						'fileName' => [
							'type'        => 'String',
							'description' => __( 'The name of the file being uploaded.', 'wp-graphql-file-upload' ),
						],
						'mimeType' => [
							'type'        => 'MimeTypeEnum',
							'description' => __( 'The mime-type of the file being uploaded.', 'wp-graphql-file-upload' ),
						],
					],
				]
			);

		}

		/**
		 * Register the file field on CreateMediaItemInput and UpdateMediaItemInput
		 */
		public function register_file_upload_field_on_media_item() {

			register_graphql_field(
				'CreateMediaItemInput',
				'file',
				[
					'type'        => 'Upload',
					'description' => 'Upload a local file using multi-part form request (file upload UI).',
				]
			);

			register_graphql_field(
				'UpdateMediaItemInput',
				'file',
				[
					'type'        => 'Upload',
					'description' => 'Upload a local file using multi-part form request (file upload UI).',
				]
			);
		}

		public function test_og_filter( $params ) {
			print_r('graphql_request_data: ');
			var_dump($params);
		}

		public function test_new_filter( $params ) {
			print_r('graphql_request_middleware: ');
			var_dump($params);
		}

	}

endif; // End if class_exists()

/**
 * Instantiate the File Upload plugin
 */
add_action(
	'plugins_loaded',
	function() {
		$file_upload = new File_Upload();
		$file_upload->init();
	}
);
