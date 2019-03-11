<?php

namespace WPGraphQL\File_Upload;

use WPGraphQL\TypeRegistry;


/**
 * Class ManageUploads
 *
 * @package WPGraphQL\File_Upload\ManageUploads
 */
class ManageUploads {

	/**
	 * Initialize the functionality for managing file uploads
	 */
	public static function init() {

		/**
		 * Filter GraphQL request data to accept a file upload from multi-part form data
		 */
		//add_filter( 'graphql_request_data', [ 'WPGraphQL\File_Upload\ManageUploads', 'test_og_filter' ] );


		/**
		 * Test GraphQL Request filter data
		 */
		//add_filter( 'graphql_request_middleware', [ 'WPGraphQL\File_Upload\ManageUploads', 'test_new_filter' ] );

		/**
		 * Add the Upload type to WPGraphQL
		 */
		add_action( 'graphql_register_types', [ 'WPGraphQL\File_Upload\ManageUploads', 'add_upload_type' ] );

		/**
		 * Add the "File" field to the createMediaItem & updateMediaItem mutations
		 */
		add_action( 'init_graphql_request', [ 'WPGraphQL\File_Upload\ManageUploads', 'register_file_upload_field_on_media_item' ] );

	}

	public function test_og_filter( $params ) {
		print_r('graphql_request_data: ');
		var_dump($params);
	}

	public function test_new_filter( $params ) {
		print_r('graphql_request_middleware: ');
		var_dump($params);
	}

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

}