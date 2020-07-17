<?php


class Ale_Xlsx_Order_Public {

	private $ale_xlsx_order;
	private $version;

	public function __construct( $ale_xlsx_order, $version ) {
		$this->view = new Ale_Xlsx_Order_View();
		$this->ale_xlsx_order = $ale_xlsx_order;
		$this->version = $version;
		$this->flash = new Ale_Xlsx_Order_Flash();
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->ale_xlsx_order, plugin_dir_url( __FILE__ ) . 'css/ale-xlsx-order-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_script( $this->ale_xlsx_order, plugin_dir_url( __FILE__ ) . 
			'js/ale-xlsx-order-public.js', array( 'jquery' ), $this->version, false );
	}

	
	public function plugin_init() {
		
		
	}

	public function init() {
		

	}

	public function wp() {
		$this->ale_xlsx_order_post();
		add_shortcode('xlsx_order', array($this, 'ale_xlsx_order'));
	}

	function upload_file() {
		if ( ! function_exists( 'wp_handle_upload' ) ) 
			require_once( ABSPATH . 'wp-admin/includes/file.php' ); 
	
		$file = & $_FILES['requisite_file'];
		$overrides = [ 'test_form' => false ];
		$uploadedFileArr = wp_handle_upload( $file, $overrides );
		return $uploadedFileArr;
	}

	public function ale_xlsx_order_post() {
		
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}

		if( !isset($_POST['action']) || $_POST['action'] != 'post_order_form') {
			return;
		}

		global $wpdb;
		global $wp;

		$redirect = $_POST['redirect'];
		
		$order = [];
		$products = $_POST['products'] ? $_POST['products']: []; 
		$order['products'] = $products;
		foreach($_POST as $k=>$v) {
			if( in_array($k, ['products','requisite_file','action','redirect' ])) {continue;}
			if(isset($v)) {
				$order[$k] = $v;
			} else {
				$order[$k] = '';
			}
		}
		
		if($_FILES['requisite_file']['name']) {
			$uploadedFileArr = $this->upload_file();
			if ( isset($uploadedFileArr['error']) ) {
				$error = new WP_Error();
				$error->add('upload_error', $uploadedFileArr['error'] );

				$this->flash->setErrors( $error);
				wp_safe_redirect( $redirect );
				exit();
			} else {
				$order['requisite_file'] = $uploadedFileArr['file'];
				
			}
		} else {
			$order['requisite_file'] = '';
		}
		
		

		$this->send_xlsx_order_mail($order);

		$this->save_order($order);

		$this->flash->setMessages([
				'success'=> __( 'Спасибо, ваша заявка принята.'
									, ALE_XLSX_ORDER
		)]);

		wp_safe_redirect( $redirect );
		exit();
		
	}

	public function save_order($order) {
		global $wpdb;
		$productsIds = array_keys($order['products']);
		$inStr = implode(',',$productsIds);
		$rows = $wpdb->get_results( "SELECT * FROM ".ALE_PRODUCTS_TABLE. " WHERE `id` IN (". $inStr .");" );
		foreach($rows as &$r) {
			$r->quantity = $order['products'][$r->id];
		}
		
		$order['products'] = json_encode($rows);
		$wpdb->insert( ALE_ORDERS_TABLE, $order ) ;
	}

	public function ale_xlsx_order($params) {
		global $wpdb;
		global $wp;
		
		$messages = $this->flash->getMessages();
		
		$errors = $this->flash->getErrors();
		
		$rows = $wpdb->get_results( "SELECT * FROM ".ALE_PRODUCTS_TABLE );

		
		$redirect = home_url($_SERVER['REQUEST_URI']);
	
		return $this->view->render(
			plugin_dir_path(  __FILE__  ) . '/templates/products_table.php',
			[
				'messages' => $messages,
				'rows' => $rows,
				'errors'=>$errors,
				'redirect' =>$redirect,
			]
		);	
	}

	public function send_xlsx_order_mail($order) {
		global $wpdb;
		$productsIds = array_keys($order['products']);
		$inStr = implode(',',$productsIds);
		

		$rows = $wpdb->get_results( "SELECT * FROM ".ALE_PRODUCTS_TABLE. " WHERE `id` IN (". $inStr .");" );
		foreach($rows as &$r) {
			$r->quantity = $order['products'][$r->id];
		}
		
		$message = $this->view->render(
			plugin_dir_path(  __FILE__  ) . '/templates/palce_order_mail.php',
			[
				'rows'=>$rows,
				'order' => $order,
			]
		);	
		
		
		$to = get_bloginfo('admin_email');
		//$to ='blyakher85@gmail.com';

		$subject ='Клиент оформил заказ';
		$attachments =[];
		if($order['requisite_file']) {
			$attachments['requisite_file'] = $order['requisite_file'];
		}

		$headers = array(
			'From: Me Myself <me@example.net>',
			'content-type: text/html',
			
		);
		
		wp_mail( $to, $subject, $message, $headers, $attachments );
	}



}
