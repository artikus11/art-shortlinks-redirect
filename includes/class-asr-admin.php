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

		add_filter( 'manage_edit-asr_redirect_columns', array( $this, 'update_redirect_columns' ), 10, 1 );
		add_action( 'manage_posts_custom_column', array( $this, 'redirect_columns_data' ), 10, 2 );

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
	 * Изменение штатных колонок в листинге постов
	 *
	 * @since 1.5.0
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function update_redirect_columns( $columns ) {

		$columns = array(
			'cb'             => $columns['cb'],
			'ID'             => 'ID',
			'title'          => 'Заголовок',
			'count_redirect' => '<span class="dashicons dashicons-controls-repeat"></span>',
			'r_terms'        => 'Рубрики',
			'r_links'        => 'Ссылка',
			'url_post'       => 'Редирект',
		);

		return $columns;
	}


	/**
	 * Для ккаждой колонки выводим свой контент
	 *
	 * @since 1.5.0
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function redirect_columns_data( $column, $post_id ) {

		switch ( $column ) {
			case 'ID':
				echo '<span>' . absint( $post_id ) . '</span>';
				break;
			case 'count_redirect':
				$count = get_post_meta( $post_id, 'redirect_count', true );
				echo $count ? absint( $count ) : 0;
				break;
			case 'url_post':
				?>
				<div class="url-redirect">
					<span
						class="asr-clipboard" data-clipboard-target="#asr-url-redirect-link-<?php echo absint( $post_id ); ?>" data-clipboard-action="copy"
						title="Скопировать в буфер обмена" aria-label="Скопировано">
						<img
							src="<?php echo esc_url( ASR_PLUGIN_URI ) . '/assets/images/clippy.svg'; ?>"
							alt="Скопировать в буфер обмена">
					</span>
					<span class="asr-url-redirect-link" id="asr-url-redirect-link-<?php echo absint( $post_id ); ?>"><?php echo esc_url( get_permalink( $post_id ) ); ?></span>
				</div>
				<div class="short-url-redirect">
					<span
						class="asr-clipboard" data-clipboard-target="#asr-short-url-redirect-<?php echo absint( $post_id ); ?>" data-clipboard-action="copy"
						title="Скопировать в буфер обмена" aria-label="Скопировано">
						<img
							src="<?php echo esc_url( ASR_PLUGIN_URI ) . '/assets/images/clippy.svg'; ?>"
							alt="Скопировать в буфер обмена">
					</span>
					<span class="asr-url-redirect-link" id="asr-short-url-redirect-<?php echo absint( $post_id ); ?>">
						<small>
							<?php echo esc_url( wp_get_shortlink( $post_id ) ); ?>
						</small>
					</span>
				</div>
				<?php

				break;
			case 'r_links':
				?>
				<input
					type="text"
					name="redirect_link"
					title="оригинальная ссылка"
					style="width: 100%;"
					class="redirect_link"
					data-redirect-id="<?php echo absint( $post_id ); ?>"
					value="<?php echo esc_url( get_post_meta( $post_id, 'asr_redirect_links_one', true ) ); ?>"/>
				<span class="spinner"></span>
				<?php

				break;
			case 'r_terms':
				$this->get_by_terms( $post_id );
				break;
		}

	}


	/**
	 * Полчение спика рубрик для каждого поста
	 *
	 * @since 1.5.0
	 *
	 * @param $post_id
	 */
	public function get_by_terms( $post_id ) {

		$terms = get_the_terms( $post_id, 'asr_redirect_cat' );

		$count = $terms ? count( $terms ) : null;

		$i = 0;

		$term_list = '';

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $cur_term ) {
				$i ++;

				$term_list .= '<a href="' . esc_url( get_edit_term_link( (int) $cur_term->term_id, $cur_term->taxonomy ) ) . '">' . esc_html( $cur_term->name ) . '</a>';
				if ( $count !== $i ) {
					$term_list .= ' &middot; ';
				} else {
					$term_list .= '</p>';
				}
			}
			echo $term_list; // WPCS XSS ok
		} else {
			echo '&mdash;';
		}
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

				wp_redirect( esc_url_raw( $redirect_url ), 301 ); // phpcs:ignore WordPress.Security.SafeRedirect
				$count = (int) get_post_meta( get_the_ID(), 'redirect_count', true );

				update_post_meta( get_the_ID(), 'redirect_count', $count + 1 );

				exit;
			}
		}
	}

}

new ASR_Admin();
