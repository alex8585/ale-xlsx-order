<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ale_Xlsx_Order
 * @subpackage Ale_Xlsx_Order/includes
 * @author     Your Name <email@example.com>
 */
class Ale_Xlsx_Order_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$sqlProducts = "CREATE TABLE IF NOT EXISTS " . ALE_PRODUCTS_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			number mediumint(9) NOT NULL,
			name text NOT NULL,
			price float,
			nds float,
			price_nds float,
			weight float,
			UNIQUE KEY id (id)
		)ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;";

		 
		$sqlOrders = "CREATE TABLE IF NOT EXISTS " . ALE_ORDERS_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			company_name varchar(255) NOT NULL,
			mail varchar(255) NOT NULL,
			phone int NOT NULL,
			requisite text DEFAULT '',
			additionally text DEFAULT '',
			requisite_file varchar(255) DEFAULT '',
			products LONGTEXT DEFAULT '',
			UNIQUE KEY id (id)
			
		)ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sqlProducts);
		dbDelta($sqlOrders);
	}

}
