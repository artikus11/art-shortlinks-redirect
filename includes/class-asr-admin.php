<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class ASR_Admin
 *
 * Основной класс для всех админских ухищрений: аякс обновление поля, копирование и буфер, редирект и тд
 *
 * @author Artem Abramovich
 * @since  1.5.0
 */
class ASR_Admin {

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'update_link_admin_script' ) );
		add_action( 'wp_ajax_update_redirect_url', array( $this, 'update_redirect_url_callback' ) );
		add_action( 'template_redirect', array( $this, 'force_redirect' ) );

	}


	/**
	 * Подключение скриптов, стилей
	 *
	 * @since 1.5.0
	 */
	public function update_link_admin_script() {

		wp_enqueue_script( 'asr-ajax', ASR_PLUGIN_URI . '/assets/js/asr-ajax.js', array( 'jquery' ), ASR_PLUGIN_VER, false );
		wp_enqueue_script( 'asr-clipboard', ASR_PLUGIN_URI . '/assets/js/clipboard.min.js', array( 'jquery' ), ASR_PLUGIN_VER, false );
		wp_enqueue_script( 'asr-scripts', ASR_PLUGIN_URI . '/assets/js/asr-scripts.js', array( 'jquery', 'asr-clipboard' ), ASR_PLUGIN_VER, false );

		wp_enqueue_style( 'asr-styles', ASR_PLUGIN_URI . '/assets/css/asr-styles.min.css', array(), ASR_PLUGIN_VER, 'all' );

		wp_localize_script(
			'asr-ajax',
			'asr_ajax',
			array(
				'nonce' => wp_create_nonce( 'asr-ajax-nonce' ),
			)
		);
	}

	/**
	 * Возвратная функция ajax-запроса
	 *
	 * @since 1.5.0
	 *
	 * @param $post_id
	 */
	public function update_redirect_url_callback( $post_id ) {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'asr-ajax-nonce' ) ) {
			wp_die( 'Пора обновить страницу. Нонса устарела' );
		}

		update_post_meta( $post_id, 'asr_redirect_links_one', esc_url( $_POST['redirect_val'] ) );
		update_post_meta( $_POST['redirect_id'], 'asr_redirect_links_one', esc_url( $_POST['redirect_val'] ) );

		wp_die();
	}

	/**
	 * Внешний редирек на нужную ссылку и подсчет кликов
	 *
	 * @since 1.5.0
	 */
	public function force_redirect() {

		if ( is_singular( 'asr_redirect' ) ) {

			$redirect_url = get_post_meta( get_the_ID(), 'asr_redirect_links_one', true );

			if ( $redirect_url ) {

				wp_redirect( esc_url( $redirect_url ), 301 ); // phpcs:ignore WordPress.Security.SafeRedirect
				$count = (int) get_post_meta( get_the_ID(), 'redirect_count', true );

				update_post_meta( get_the_ID(), 'redirect_count', $count + 1 );

				exit;
			}
		}
	}

}

new ASR_Admin();
