<?php

define("ALE_XLSX_ORDER", 'ale-xlsx-order');

define("ALE_PRODUCTS_TABLE", 'ale_xlsx_products');
define("ALE_ORDERS_TABLE", 'ale_xlsx_orders');


if(function_exists('print_filters_for') ) {
    function print_filters_for( $hook = '' ) {
        global $wp_filter;
        if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
            return;
    
        print '<pre>';
        print_r( $wp_filter[$hook] );
        print '</pre>';
    }
}

