<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/admin
 * @author     Your Name <email@example.com>
 */
class Ale_Xlsx_Order_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ale_xlsx_order    The ID of this plugin.
	 */
	private $ale_xlsx_order;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $ale_xlsx_order       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $ale_xlsx_order, $version ) {

		$this->ale_xlsx_order = $ale_xlsx_order;
		$this->version = $version;
		$this->upload_page = ALE_XLSX_ORDER;
		$this->view = new Ale_Xlsx_Order_View();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->ale_xlsx_order, plugin_dir_url( __FILE__ ) . 'css/ale-xlsx-order-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->ale_xlsx_order, plugin_dir_url( __FILE__ ) . 'js/ale-xlsx-order-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function admin_init() {
		//add_filter(  'plugin_action_links' ,  [$this,'action_links' ],10, 2);
	}

	public function action_links( $links, $plugin_file ) {
		if( false === strpos( $plugin_file, 'ale-xlsx-order' ) )
			return $links;
		
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'spyr-authorizenet-aim' ) . '</a>',
		);
	
		return array_merge( $plugin_links, $links );	
	}
	

	public function plugin_init() {
		add_action('admin_menu', [$this,'xls_order_menu']);
	
	}


	public function xls_order_menu() {
		add_menu_page( 
			__('XLSX Импорт', ALE_XLSX_ORDER),
			__('XLSX Импорт', ALE_XLSX_ORDER),  
			'manage_options', 
			$this->upload_page,
			[$this, 'xls_order_menu_page'], 
			'dashicons-products', 
			120
		);
		add_submenu_page(
			$this->upload_page, 
			__('Заказы', ALE_XLSX_ORDER),
			__('Заказы', ALE_XLSX_ORDER),  
			'manage_options',  
			$this->upload_page .'_orders',
			[$this, 'xls_order_menu_orders']
		 );
		 add_submenu_page(
			null, 
			__('Товары заказа', ALE_XLSX_ORDER),
			__('Товары заказа', ALE_XLSX_ORDER),  
			'manage_options',  
			$this->upload_page .'_products',
			[$this, 'xls_order_menu_order_products']
	 	);
	
	}

	public function xls_order_menu_order_products() {
		global $wpdb;
		$id = isset($_GET['id']) ? ( int )$_GET['id'] :'';
		if(!$id) return;
		
		$order = $wpdb->get_row( "SELECT * FROM ".ALE_ORDERS_TABLE. " WHERE `id` = ".$id.';'  );
		$rows = json_decode($order->products);
		
		
		echo $this->view->render(
			plugin_dir_path(  __FILE__  ) . 'templates/products_table.php',
			[
				'rows'=>$rows ,
				'order'=>$order
			]
		);	
	}

	public function xls_order_menu_orders() {
		global $wpdb;

		$perPage = 20;
		$page = isset($_GET['p']) ? $_GET['p'] : 1;
		$cntRow = $wpdb->get_row( "SELECT COUNT(*) as cnt FROM ".ALE_ORDERS_TABLE );
		if(!$cntRow->cnt) {
			echo '<br><div class="no-orders">Нет заказов в базе.</div>';
			return;
		}

		$paginator = (new Paginator($page, $cntRow->cnt,  $perPage))->get_pages();
		

		$orders = $wpdb->get_results( "SELECT * FROM ".ALE_ORDERS_TABLE.  " ORDER BY id ". 
		" LIMIT ". $paginator['sql_limit_from'] .", " . $perPage .';' );
		
		echo $this->view->render(
			plugin_dir_path(  __FILE__  ) . 'templates/orders_table.php',
			[
				'orders'=>$orders ,
				'paginator'=>$paginator,
				'products_page' => admin_url( 'admin.php?page='.$this->upload_page .'_products' ),
				'orders_page' => admin_url( 'admin.php?page='.$this->upload_page .'_orders' ),
			]
		);	
		
	}



	public function xls_order_menu_page(){

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ( ! function_exists( 'wp_handle_upload' ) ) 
				require_once( ABSPATH . 'wp-admin/includes/file.php' ); 

			$file = & $_FILES['file_upload'];

			$overrides = [ 'test_form' => false ];

			$uploadedFileArr = wp_handle_upload( $file, $overrides );

			if ( $uploadedFileArr && empty($uploadedFileArr['error']) ): ?> 
				<?php $p = $this->parce_xlsx($uploadedFileArr['file']);
				if($p['result']):  ?>
					<div class="notice notice-success is-dismissible">
						<p><?php _e('XLSX импортирован', ALE_XLSX_ORDER); ?></p>
					</div>
				<?php else: ?>
					<div class="notice notice-error is-dismissible"> 
						<p>
							<?php _e('Ошибка парсинга, не правильный формат файла', ALE_XLSX_ORDER) ?>
						</p>
						<p>
							<?php echo $p['error'] ?>
						</p>
					</div>
				<?php endif; ?>

			<?php else: ?>
				<div class="notice notice-error is-dismissible"> 
					<p><?php _e('Ошибка загрузки файла', ALE_XLSX_ORDER) ?></p>
				</div>
			<?php endif; 
		}

		?>
			<div class="wrap">
				<h2> <?php _e('Импорт XLSX файла', ALE_XLSX_ORDER) ?></h2>
				<form method="post" enctype="multipart/form-data" action="
				<?php echo admin_url( 'admin.php?page='.$this->upload_page )?>">
					<input name="file_upload" type="file" />
					<p class="submit">  
						<input type="submit" class="button-primary" value="<?php _e('Импортировать') ?>" />  
					</p>
				</form>
			</div>
			
		<?php

		

	}

	function parce_xlsx($file) {
		global $wpdb;
		
		if ( $xlsx = SimpleXLSX::parse($file) ) {
			$rows = $xlsx->rows();
			if(!$rows) {
				return [
					'result' => false,
					'error' => ''
				];
			} 
			$wpdb->query("TRUNCATE TABLE ". ALE_PRODUCTS_TABLE);
			$i = 0;
			$rowsToDb = [];
			foreach($rows as $xlsRow ) {
				$i++; if($i <= 2) {continue;}
				if(!$xlsRow[1]) {continue;}

				$row['number'] = $i-2;
				$row['name'] = $xlsRow[1];
				$row['price'] =  $xlsRow[2];
				$row['nds'] = $xlsRow[3];
				$row['price_nds'] = $xlsRow[5];
				$row['weight'] =  $xlsRow[6];
				$rowsToDb[] = $row;
				$wpdb->insert( ALE_PRODUCTS_TABLE, $row ) ;
			}
			return [
				'result' => true,
			];
		} else {
			return [
				'result' => false,
				'error' => SimpleXLSX::parseError()
			];
			
			
		}
	}
	

}

