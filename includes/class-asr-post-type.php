<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class ASR_Post_Types
 *
 * Класс для создания типа поста и таксономии к нему
 *
 * @class       ASR_Shortlinks_Redirect
 * @since       1.5.0
 * @author      Artem Abramovich
 */
class ASR_Post_Types {

	/**
	 * Хуки и методы
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 6 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}


	/**
	 * Регистрируем таксономию
	 *
	 * @since 1.5.0
	 */
	public static function register_taxonomies() {

		register_taxonomy(
			'asr_redirect_cat',
			array( 'asr_redirect' ),
			apply_filters(
				'asr_taxonomy_args_redirect_cat',
				array(
					'hierarchical' => true,
					'label'        => 'Рубрики',
					'labels'       => array(
						'name'          => 'Рубрики редиректов',
						'singular_name' => 'Рубрика',
						'menu_name'     => 'Рубрики редиректов',
					),
					'show_ui'      => true,
					'query_var'    => true,
					'rewrite'      => array(
						'slug'         => 'rc',
						'with_front'   => true,
						'hierarchical' => true,
					),
				)
			)
		);
	}


	/**
	 * Регистрируем тип записи
	 *
	 * @since 1.0.0
	 */
	public static function register_post_types() {

		register_post_type(
			'asr_redirect',
			apply_filters(
				'asr_register_post_type',
				array(
					'labels'              => array(
						'name'               => 'Редиректы',
						'singular_name'      => 'Редирект',
						'add_new'            => 'Добавить редирект',
						'add_new_item'       => 'Новый редирект',
						'edit_item'          => 'Редактировать редирект',
						'new_item'           => 'Новый редирект',
						'view_item'          => 'Просмотр редиректа',
						'search_items'       => 'Искать редирект',
						'not_found'          => 'Не найдено',
						'not_found_in_trash' => 'В корзине не найдено',
						'menu_name'          => 'Редиректы',
					),
					'public'              => true,
					'exclude_from_search' => true,
					'show_in_menu'        => true,
					'menu_position'       => 27,
					'menu_icon'           => 'dashicons-controls-repeat',
					'hierarchical'        => false,
					'supports'            => array( 'title' ),
					'taxonomies'          => array( 'asr_redirect_cat' ),
					'has_archive'         => false,
					'rewrite'             => array(
						'slug'       => 'r',
						'with_front' => true,
					),
					'query_var'           => false,
					'show_in_nav_menus'   => true,
				)
			)
		);
	}

}

ASR_Post_Types::init();
