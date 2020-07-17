<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/includes
 * @author     Your Name <email@example.com>
 */
class Ale_Xlsx_Order {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ale_Xlsx_Order_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ale_xlsx_order    The string used to uniquely identify this plugin.
	 */
	protected $ale_xlsx_order;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ALEUPPC_PRICE_CONVERTER_VERSION' ) ) {
			$this->version = ALEUPPC_PRICE_CONVERTER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->ale_xlsx_order = 'ale-xlsx-order';

		

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		
	}


	private function define_public_hooks() {

		$plugin_public = new Ale_Xlsx_Order_Public( $this->get_ale_xlsx_order(), $this->get_version() );
		
		$plugin_public->plugin_init();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'init' );
		$this->loader->add_action( 'wp', $plugin_public, 'wp' );

		//$this->loader->add_action( 'wp', $plugin_public, 'reg_shortcodes' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	 private function define_admin_hooks() {

		$plugin_admin = new Ale_Xlsx_Order_Admin( $this->get_ale_xlsx_order(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$plugin_admin->plugin_init();
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );

	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ale_Xlsx_Order_Loader. Orchestrates the hooks of the plugin.
	 * - Ale_Xlsx_Order_i18n. Defines internationalization functionality.
	 * - Ale_Xlsx_Order_Admin. Defines all hooks for the admin area.
	 * - Ale_Xlsx_Order_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ale-xlsx-order-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ale-xlsx-order-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ale-xlsx-order-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ale-xlsx-order-public.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ale-xlsx-order-global.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ale-xlsx-order-view.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ale-xlsx-order-flash.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ale-xlsx-order-paginator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/SimpleXLSX.php';

		

		$this->loader = new Ale_Xlsx_Order_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ale_Xlsx_Order_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ale_Xlsx_Order_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_ale_xlsx_order() {
		return $this->ale_xlsx_order;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ale_Xlsx_Order_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
