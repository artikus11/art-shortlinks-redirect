<?php
/**
 * Plugin Name: Art Shortlinks Redirect
 * Plugin URI: https://wpruse.ru/?p=398
 * Text Domain: art-shortlinks-redirect
 * Domain Path: /languages
 * Description: Плагин редиректов. Создает отдельные произвольные записи, с помощью которых можно через 301-й редирект перенаправлять пользователей на нужные страницы. Разработан специально для ютуб блогеров. С помощью плагина можно легко и непринужденно добавлять ссылки в аннотациях (сайт через который идет редирект должен быть привязан к каналу) и просто использовать, как собственную систему коротких ссылок.
 * Version: 1.5.1
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
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

$asr_data = get_file_data(
	__FILE__,
	array(
		'ver'         => 'Version',
		'name'        => 'Plugin Name',
		'text_domain' => 'Text Domain',
	)
);

define( 'ASR_PLUGIN_VER', $asr_data['ver'] );
define( 'ASR_PLUGIN_NAME', $asr_data['name'] );

/**
 * Сброс постоянных сылок при активации плагина
 *
 * @version     1.5.0
 */
register_activation_hook( __FILE__, array( 'ASR_Shortlinks_Redirect', 'flush_rewrite_rules' ) );

/**
 * Class ASR_Shortlinks_Redirect
 *
 * Main ASR_Shortlinks_Redirect class, initialized the plugin
 *
 * @class       ASR_Shortlinks_Redirect
 * @since       1.5.0
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

		require_once ASR_PLUGIN_DIR . 'includes/class-asr-post-type.php';
		require_once ASR_PLUGIN_DIR . 'includes/class-asr-metabox.php';
		require_once ASR_PLUGIN_DIR . 'includes/class-asr-admin.php';

	}


	/**
	 * Сброс постоянных ссылок
	 *
	 * @since 1.5.0
	 */
	public static function flush_rewrite_rules() {

		flush_rewrite_rules();
	}


	/**
	 * Instance.
	 *
	 * Глобальный экземпляр класса. Используется для извлечения экземпляра
	 * использовать для других файлов/плагинов/тем
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
	 * Проверка на версию PHP.
	 *
	 * Выводит сообщение если версия PHP ниже 5.6
	 *
	 * @since 1.5.0
	 */
	public function php_version_notice() {

		?>
		<div class="notice notice-error">
			<p>
				<?php

				printf(/* translators: 1: name plugin, 2: php version */
					esc_html__(
						'%1$s требует версию PHP 5.6 или выше. Ваша текущая версия PHP %2$s. Пожалуйста, обновите версию PHP.',
						'art-decoration-shortcode'
					),
					esc_html( ASR_PLUGIN_NAME ),
					PHP_VERSION
				);
				?>
			</p>
		</div>
		<?php

	}

}

/**
 * Основная функция, отвечающая за возврат объекта ASR_Shortlinks_Redirect.
 *
 * Используйте эту функцию, как глобальную переменную, за исключением того, что не нужно объявлять глобально.
 *
 * Пример: <?php ASR_Shortlinks_Redirect()->method_name(); ?>
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

//Обратная совместимость
$GLOBALS['asr'] = asr_shortlinks_redirect();
