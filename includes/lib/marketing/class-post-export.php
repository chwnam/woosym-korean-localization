<?php

require_once( WSKL_PATH . '/includes/lib/auth/class-wskl-auth-info.php' );
require_once( WSKL_PATH . '/includes/lib/cassandra-php/class-api-handler.php' );

use wskl\lib\cassandra\PostAPI;


if( !defined( 'LAST_POST_EXPORT' ) ) {
	define( 'LAST_POST_EXPORT', wskl_get_option_name( 'last_post_export' ) );
}


class WSKL_Post_Export {

	public static function initialize() {

		$auth_info = new WSKL_Auth_Info( 'marketing' );

		if ( ! $auth_info->is_verified() ) {

			self::not_authorized_output();

			return;
		}

		/**
		 * @see wordpress/wp-admin/includes/meta-boxes.php post_submit_meta_box()
		 */
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'callback_post_submitbox_misc_actions' ), 99, 1 );
		add_action( 'save_post', array( __CLASS__, 'callback_save_post' ), 99, 3 );
	}

	public static function not_authorized_output() {


	}

	public static function callback_post_submitbox_misc_actions( \WP_Post $post ) {

		$context = array(
			'last_export' => get_post_meta( $post->ID, LAST_POST_EXPORT, TRUE )
		);

		$default_path = __DIR__ . '/templates/';
		wc_get_template( 'post_submitbox_misc.php', $context, '', $default_path );
	}

	public static function callback_save_post( $post_id, \WP_Post $post, $update ) {

		if ( !$update || defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		$is_export_allowed = filter_var( $_POST['dabory-post-export'], FILTER_VALIDATE_BOOLEAN );
		if( ! $is_export_allowed ) {
			return;
		}

		$auth = new \WSKL_Auth_Info( 'marketing' );

		if ( $auth->is_verified() ) {

			$key_type  = $auth->get_key_type();
			$key_value = $auth->get_key_value();
			$user_id   = $auth->get_oir()->get_user_id();

			$site_url = site_url();

			PostAPI::send_post( $key_type, $key_value, $site_url, $user_id, $post_id );

			update_post_meta( $post_id, LAST_POST_EXPORT, time() );
		}
	}
}


WSKL_Post_Export::initialize();