<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class ASR_Metabox
 *
 * Создание отдельного метабокса для получения внешней ссылки
 *
 * @author Artem Abramovich
 * @since  1.5.0
 */
class ASR_Metabox {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'redirect_links_meta_box' ) );
		add_action( 'save_post', array( $this, 'redirect_links_save_box' ) );
	}


	/**
	 * Регистрируем метабокс для типа записи asr_redirect
	 *
	 * @since 1.5.0
	 */
	public function redirect_links_meta_box() {

		add_meta_box(
			'asr-metabox',
			'Редирект',
			array( $this, 'redirect_links' ),
			'asr_redirect',
			'normal',
			'high'
		);
	}


	/**
	 * Добавляем поле ввода для ссылки
	 *
	 * @since 1.5.0
	 *
	 * @param $post
	 */
	public function redirect_links( $post ) {

		$value = get_post_meta( $post->ID, 'asr_redirect_links_one', true );
		wp_nonce_field( basename( __FILE__ ), 'redirect_links_nonce' );
		echo '<label><input style="width: 100%;" type="text" name="redirectlinks" value="' . esc_url_raw( $value ) . '" /></label> ';

	}


	/**
	 * Сохраняем значение поля
	 *
	 * @since 1.5.0
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function redirect_links_save_box( $post_id ) {

		if ( ! isset( $_POST['redirect_links_nonce'] ) || ! wp_verify_nonce( $_POST['redirect_links_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$post = get_post( $post_id );

		if ( 'asr_redirect' === $post->post_type ) {
			update_post_meta( $post_id, 'asr_redirect_links_one', esc_url_raw( $_POST['redirectlinks'] ) );
		}

		return $post_id;
	}

}

new ASR_Metabox();
