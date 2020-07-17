jQuery( function( $ ) {
	
	// woocommerce_price_slider_params is required to continue, ensure the object exists
	if ( typeof woocommerce_price_slider_params === 'undefined' ) {
		return false;
	}

	$( document.body ).bind( 'weight_slider_create weight_slider_slide', function( event, min, max ) {
		
		$( '.weight_slider_amount span.from' ).html( accounting.formatMoney( min, {
			symbol:    weight_slider_params.unit,
			decimal:   woocommerce_price_slider_params.currency_format_decimal_sep,
			thousand:  '',
			precision: woocommerce_price_slider_params.currency_format_num_decimals,
			format:    woocommerce_price_slider_params.currency_format
		} ) );

		$( '.weight_slider_amount span.to' ).html( accounting.formatMoney( max, {
			symbol:    weight_slider_params.unit,
			decimal:   woocommerce_price_slider_params.currency_format_decimal_sep,
			thousand:  '',
			precision: woocommerce_price_slider_params.currency_format_num_decimals,
			format:    woocommerce_price_slider_params.currency_format
		} ) );

		$( document.body ).trigger( 'weight_slider_updated', [ min, max ] );
	});

	function init_weight_filter() {
		$( 'input#min_weight, input#max_weight' ).hide();
		$( '.weight_slider, .weight_label' ).show();

		var min_weight         = $( '.weight_slider_amount #min_weight' ).data( 'min' ),
			max_weight         = $( '.weight_slider_amount #max_weight' ).data( 'max' ),
			step              = $( '.weight_slider_amount' ).data( 'step' ) || 1,
			current_min_weight = $( '.weight_slider_amount #min_weight' ).val(),
			current_max_weight = $( '.weight_slider_amount #max_weight' ).val();
		//console.log(current_max_weight);
		$( '.weight_slider:not(.ui-slider)' ).slider({
			range: true,
			animate: true,
			min: min_weight,
			max: max_weight,
			step: step,
			values: [ current_min_weight, current_max_weight ],
			create: function() {

				$( '.weight_slider_amount #min_weight' ).val( current_min_weight );
				$( '.weight_slider_amount #max_weight' ).val( current_max_weight );

				$( document.body ).trigger( 'weight_slider_create', [ current_min_weight, current_max_weight ] );
			},
			slide: function( event, ui ) {

				$( 'input#min_weight' ).val( ui.values[0] );
				$( 'input#max_weight' ).val( ui.values[1] );

				$( document.body ).trigger( 'weight_slider_slide', [ ui.values[0], ui.values[1] ] );
			},
			change: function( event, ui ) {

				$( document.body ).trigger( 'weight_slider_change', [ ui.values[0], ui.values[1] ] );
			}
		});
	}

	init_weight_filter();

	var hasSelectiveRefresh = (
		'undefined' !== typeof wp &&
		wp.customize &&
		wp.customize.selectiveRefresh &&
		wp.customize.widgetsPreview &&
		wp.customize.widgetsPreview.WidgetPartial
	);
	if ( hasSelectiveRefresh ) {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
			init_price_filter();
		} );
	}
});
