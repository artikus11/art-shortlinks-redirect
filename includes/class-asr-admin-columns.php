<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class ASR_Admin_Columns
 *
 * Основной класс для управления листингом постов в админке
 *
 * @author Artem Abramovich
 * @since  1.5.1
 */
class ASR_Admin_Columns {

	public function __construct() {
		add_filter( 'manage_edit-asr_redirect_columns', array( $this, 'update_redirect_columns' ), 10, 1 );
		add_action( 'manage_posts_custom_column', array( $this, 'redirect_columns_data' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'tax_filters_dropdown' ), 20, 1 );
		//add_filter( 'wpseo_use_page_analysis', array( $this, 'remove_seo_filters' ) );


	}


	public function tax_filters_dropdown( $post_type ) {
		global $cat;

		if ( 'asr_redirect' !== $post_type ) {
			return;
		}

		if ( is_object_in_taxonomy( $post_type, 'asr_redirect_cat' ) ) {
			$dropdown_options = array(
				'show_option_all' => get_taxonomy( 'asr_redirect_cat' )->labels->all_items,
				'hide_empty'      => 0,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
				'name'            => 'asr_redirect_cat',
				'taxonomy'        => 'asr_redirect_cat',
				'value_field'     => 'name',
				'selected'        => $cat,
			);

			echo '<label class="screen-reader-text" for="cat">Все рубрики</label>';
			wp_dropdown_categories( $dropdown_options );

		}
	}


	public function remove_seo_filters() {

		if ( ! is_singular( 'asr_redirect' ) ) {
			return true;

		}

		return false;
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
			'cb'                        => $columns['cb'],
			'ID'                        => 'ID',
			'title'                     => 'Заголовок',
			'count_redirect'            => '<span class="dashicons dashicons-controls-repeat"></span>',
			'taxonomy-asr_redirect_cat' => 'Рубрики',
			'r_links'                   => 'Ссылка',
			'url_post'                  => 'Редирект',
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
					<span class="asr-url-redirect-link" id="asr-url-redirect-link-<?php echo absint( $post_id ); ?>"><?php echo esc_url_raw( get_permalink( $post_id ) ); ?></span>
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

}

new ASR_Admin_Columns();
