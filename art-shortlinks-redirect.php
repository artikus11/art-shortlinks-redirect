<?php
/**
 * Plugin Name: Art Shortlinks Redirect
 * Plugin URI: https://wpruse.ru/?p=398
 * Text Domain: art-shortlinks-redirect
 * Domain Path: /languages
 * Description: Плагин редиректов. Создает отдельные произвольные записи, с помощью которых можно через 301-й редирект перенаправлять пользователей на нужные страницы. Разработан специально для ютуб блогеров. С помощью плагина можно легко и непринужденно добавлять ссылки в аннотациях (сайт через который идет редирект должен быть привязан к каналу) и просто использовать, как собственную систему коротких ссылок.
 * Version: 1.5.0
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt Text Domain: Domain Path:
 *
 * GitHub Plugin URI: https://github.com/artikus11/
 *
 * Copyright Artem Abramovich
 *
 *     This file is part of Art Shortlinks Redirect,
 *     a plugin for WordPress.
 *
 *     Art Shortlinks Redirect is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Art Shortlinks Redirect is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'ASR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASR_PLUGIN_URI', plugins_url( '', __FILE__ ) );

$asr_data = get_file_data( __FILE__, array(
	'ver'         => 'Version',
	'name'        => 'Plugin Name',
	'text_domain' => 'Text Domain',
) );

define( 'ASR_PLUGIN_VER', $asr_data['ver'] );
define( 'ASR_PLUGIN_NAME', $asr_data['name'] );


register_uninstall_hook( __FILE__, array( 'ASR_Shortlinks_Redirect', 'uninstall' ) );

/**
 * Class ASR_Shortlinks_Redirect
 *
 * Main ASR_Shortlinks_Redirect class, initialized the plugin
 *
 * @class       ASR_Shortlinks_Redirect
 * @version     1.5.0
 * @author      Artem Abramovich
 */
class ASR_Shortlinks_Redirect {
	
	/**
	 * Instance of ASR_Shortlinks_Redirect.
	 *
	 * @since  1.5.0
	 * @access private
	 * @var object $instance The instance of ASR_Shortlinks_Redirect.
	 */
	private static $instance;
	
	/**
	 * Construct.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		
		$this->init();
		
		// Load textdomain
		$this->load_textdomain();
		
	}
	
	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 *
	 * @since 1.5.0
	 */
	public function init() {
		
		if ( version_compare( PHP_VERSION, '5.6', 'lt' ) ) {
			return add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
		}
		
		if ( is_admin() ) :
			
			/**
			 * Settings
			 */
			//require_once ASR_PLUGIN_DIR . 'includes/class-asr-ctp.php';
			//$this->admin_settings = new ASM_Admin_Settings();
			
			//require_once ASR_PLUGIN_DIR . 'includes/class-asr-metabox.php';
			//$this->admin_settings = new ASM_Admin_Settings();
			
			//require_once ASR_PLUGIN_DIR . 'includes/class-asr-ajax.php';
			//$this->admin_settings = new ASM_Admin_Settings();
		
		endif;
		
		/**
		 * Front end
		 */
		//require_once ASR_PLUGIN_DIR . 'includes/class-asm-markup-data.php';
		//$this->front_end = new ASM_Markup();
		
		/*global $pagenow;
		if ( 'plugins.php' == $pagenow ) {
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		}*/
		
		
	}
	
	
	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.5.0
	 */
	public function load_textdomain() {
		
		$locale = apply_filters( 'plugin_locale', get_locale(), 'art-shortlinks-redirect' );
		
		// Load textdomain
		load_textdomain( 'art-shortlinks-redirect', WP_LANG_DIR . '/art-shortlinks-redirect/art-shortlinks-redirect-' . $locale . '.mo' );
		load_plugin_textdomain( 'art-shortlinks-redirect', false, basename( dirname( __FILE__ ) ) . '/languages' );
		
	}
	
	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.5.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		
		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;
		
		return self::$instance;
		
	}
	
	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.5.0
	 *
	 * @param    array  $links List of existing links.
	 * @param    string $file  Name of the current plugin being looped.
	 *
	 * @return    array            List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {
		
		if ( $file == plugin_basename( __FILE__ ) ) :
			$links = array_merge( array(
				'<a href="' . esc_url( admin_url( 'options-general.php?page=markup_slug' ) ) . '">' . __( 'Settings' ) . '</a>',
			), $links );
		endif;
		
		return $links;
		
	}
	
	/**
	 * Display PHP 5.6 required notice.
	 *
	 * Display a notice when the required PHP version is not met.
	 *
	 * @since 1.5.0
	 */
	public function php_version_notice() {
		
		?>
		<div class="notice notice-error">
			
			<p><?php echo sprintf( __( '%s requires PHP 5.6 or higher and your current PHP version is %s. Please (contact your host to) update your PHP version.', 'art-schema-markup' ), ASR_PLUGIN_NAME, PHP_VERSION ); ?></p>
		</div>
		<?php
		
	}
	
	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 1.5.0
	 */
	public static function uninstall() {
		
		//delete_option( 'asm_option_name' );
	}
	
}


/**
 * The main function responsible for returning the ASR_Shortlinks_Redirect object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php ASR_Shortlinks_Redirect()->method_name(); ?>
 *
 * @since 1.5.0
 *
 * @return object ASR_Shortlinks_Redirect class object.
 */
if ( ! function_exists( 'asr_shortlinks_redirect' ) ) :
	
	function asr_shortlinks_redirect() {
		
		return ASR_Shortlinks_Redirect::instance();
	}

endif;

asr_shortlinks_redirect();

// Backwards compatibility
$GLOBALS['asr'] = asr_shortlinks_redirect();


/*
* Подключаем нужные файлы
*/
function asr_r_script() {
        wp_enqueue_script('ajaxredirect',plugins_url('js/ajax_redirect.js',__FILE__), array('jquery'),true);
}
add_action( 'admin_enqueue_scripts', 'asr_r_script' );

/*
* Регистрируем новый тип записи
*/
add_action('init', 'asr_post_types_redirect');
function asr_post_types_redirect(){
	$args = array(
		'labels' => array(
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
		'supports'            => array('title'),
		'taxonomies'          => array(),
		'has_archive'         => false,
		'rewrite'  => array ( 'slug' => 'r', 'with_front' => true ), 
		'query_var'           => false,
		'show_in_nav_menus'   => true,
	);

	register_post_type('asr_redirect', $args );

}
/*
* Подключаем автоматическое обновление пермалинков
*/
register_activation_hook( __FILE__, 'asr_redirect_flush_rewrites' );
function asr_redirect_flush_rewrites() {
	asr_post_types_redirect();
	flush_rewrite_rules(false);
}
/*
* Подключаем метабоксы в произвольной записи
*/
function asr_redirect_links_meta_boxes() {
	add_meta_box('truediv', 'Редирект', 'asr_redirect_links', 'asr_redirect', 'normal', 'high');
}
add_action( 'admin_menu', 'asr_redirect_links_meta_boxes' );

function asr_redirect_links($post) {
	wp_nonce_field( basename( __FILE__ ), 'redirect_links_nonce' );
	echo '<label><input style="width: 100%;" type="text" name="redirectlinks" value="' . get_post_meta($post->ID, 'asr_redirect_links_one',true) . '" /></label> ';
	
}
 
function asr_redirect_links_save_box ( $post_id ) {
	if ( !isset( $_POST['redirect_links_nonce'] )
	|| !wp_verify_nonce( $_POST['redirect_links_nonce'], basename( __FILE__ ) ) )
        return $post_id;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	$post = get_post($post_id);
	if ($post->post_type == 'asr_redirect') { 
		update_post_meta($post_id, 'asr_redirect_links_one', esc_attr($_POST['redirectlinks']));
	}
	return $post_id;
}
 
add_action('save_post', 'asr_redirect_links_save_box');

/*
* Подключаем отдельный файл редиректа
*/
function asr_force_template( $template ){	
	if( is_singular( 'asr_redirect' ) ) {
		global $post;
        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/inc/redirect-template.php';
		$count_meta = get_post_meta( $post->ID, 'redirect_count', true );
		$count = isset( $post->ID) ? $count_meta : 0;
		update_post_meta( $post->ID, 'redirect_count', $count + 1 );
	}
     return $template;
}
add_filter( 'template_include', 'asr_force_template' );

/*
* Подключаем и изменяем колонки в админке
*/
function art_redirect_add_columns($my_columns){
	unset($my_columns['date']);
	$add_columns = array( 
		'count_redirect' => '<span class="dashicons dashicons-controls-repeat"></span>',
		'url_post' => 'Ссылка', 
		'short_url_post' => 'Короткая сcылка', 
		'r_links' => 'Редирект'
		);
	$my_columns = array_slice( $my_columns, 0, 2, true ) + $add_columns + array_slice( $my_columns, 2, NULL, true );
	return $my_columns;
}
 
function art_redirect_fill_post_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'count_redirect':
		    $count = isset($post->ID) ? get_post_meta( $post->ID, 'redirect_count', true ) : 0; 
			echo '<style>
			th.column-count_redirect, td.column-count_redirect {
			text-align: center;
			width: 5%;
			}
			</style>'. absint( $count );
			break;
	}
	switch ( $column ) {
		case 'url_post':
			echo '<input type="text" disabled="disabled" style="width: 100%;" class="url_post_link" value="' . get_permalink($post->ID) . '" />';
			break;
	}
	switch ( $column ) {
		case 'short_url_post':
			echo '<input type="text" disabled="disabled" style="width: 100%;" class="short_url_post_link" value="' . wp_get_shortlink($post->ID) . '" />';
			break;
	}
	switch ( $column ) {
		case 'r_links':
			echo '<input type="text" style="width: 100%;" class="redirect_link" data-id="' . $post->ID .'" value="' . get_post_meta( $post->ID, 'asr_redirect_links_one', true ) . '" /><p></p>';
			break;
	}


}
add_filter( 'manage_edit-asr_redirect_columns', 'art_redirect_add_columns', 10, 1 ); 
add_action( 'manage_posts_custom_column', 'art_redirect_fill_post_columns', 10, 1 );

function update_redirect_url_callback(){ 
	update_post_meta($post_id, 'asr_redirect_links_one', esc_attr($_POST['redirectlinks']));
	update_post_meta($_POST['redirect_id'], 'asr_redirect_links_one', esc_attr($_POST['redirect_val']));
	die();
}
 
if( is_admin() ) {
	add_action('wp_ajax_update_redirect_url', 'update_redirect_url_callback');
}
