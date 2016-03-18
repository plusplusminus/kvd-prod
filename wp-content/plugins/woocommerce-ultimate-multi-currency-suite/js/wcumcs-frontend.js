/**
 * 
 * Plugin: WooCommerce Ultimate Multi Currency Suite
 * Author: http://dev49.net
 *
 */



/* ---------------------------- INVISIBLE FORM ---------------------------- */

/* Create invisible form which will be submitting currency change data */

	jQuery(document).ready(function ($) {

		$('body').append('<form method="post" id="wcumcs-currency-form" style="display:none"><input id="wcumcs-currency-input" type="text" name="' + wcumcs_vars_data.currency_change_key + '" /></form>')

	})


	/* ---------------------------- WC COMPOSITE PRODUCTS COMPATIBILITY ---------------------------- */

	/* If conversion in reference method, convert total price  */

	if (wcumcs_vars_data.conversion_method == 'reference' && // reference conversion method and...
		wcumcs_vars_data.base_currency != wcumcs_vars_data.currency && // active currency different than base currency and...
		wcumcs_vars_data.currency_data['rate'] != 1) { // exchange rate different than 1) 

		jQuery(document).ready(function ($) {

			var observerNode = '.composite_form .composite_data'; // we're going to hook into this selector and catch its changes

			if ($(observerNode).length > 0) { // if WC composite products is used

				$(function () {

					(function ($) {
						var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
						$.fn.objectChange = function (callback) {
							if (MutationObserver) {
								var options = {
									childList: true,
									attributes: true,
									characterData: true,
									subtree: true
								};
								if (typeof observer == "undefined") {
									var observer = new MutationObserver(function (mutations) {
										mutations.forEach(function (e) {
											callback.call(e.target);
										});
									});
								}
								return this.each(function () {
									observer.observe(this, options);
								});
							}
						}
					})(jQuery);

					update_totals = function () { // this function is called every time there is any change made to the observerNode
						$('.cart.composite_data').each(function () {
							var price_selector = '.composite_price';
							var price_html = '';
							var current_price_html = $(price_selector, this).html();
							var regular_total = $(this).data('price_data').regular_total;
							var total = $(this).data('price_data').total;
							var show_free_string = $(this).data('price_data').show_free_string;
							var symbol = wcumcs_vars_data.currency_data.symbol;
							var rate = wcumcs_vars_data.currency_data.rate;
							var position = wcumcs_vars_data.currency_data.position;
							var number_decimals = wcumcs_vars_data.currency_data.number_decimals;
							var decimal_separator = wcumcs_vars_data.currency_data.decimal_separator;
							var thousand_separator = wcumcs_vars_data.currency_data.thousand_separator;
							var is_on_sale = false;
							if (regular_total > total) { // on sale
								is_on_sale = true;
							}

							function format_price(number) { // retrun fully formatted price with symbol, separators etc.
								var n = number, c = isNaN(number_decimals = Math.abs(number_decimals)) ? 2 : number_decimals;
								var d = typeof (decimal_separator) === 'undefined' ? '.' : decimal_separator;
								var t = typeof (thousand_separator) === 'undefined' ? ',' : thousand_separator, s = n < 0 ? '-' : '';
								var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '', j = (j = i.length) > 3 ? j % 3 : 0;
								var formatted_number = s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');

								var formatted_number_with_currency = '';

								if (position == 'left') {
									formatted_number_with_currency = '<span class="amount">' + symbol + formatted_number + '</span>';
								} else if (position == 'right') {
									formatted_number_with_currency = '<span class="amount">' + formatted_number + symbol + '</span>';
								} else if (position == 'left_space') {
									formatted_number_with_currency = '<span class="amount">' + symbol + ' ' + formatted_number + '</span>';
								} else if (position == 'right_space') {
									formatted_number_with_currency = '<span class="amount">' + formatted_number + ' ' + symbol + '</span>';
								}

								return formatted_number_with_currency;

							}

							if (total == 0 && show_free_string === 'yes') { // total cost is 0 and we should be showing Free label (instead of 0)

								price_html = '<p class="price"><span class="total">' + wc_composite_params.i18n_total + '</span>' + wc_composite_params.i18n_free + '</p>';

							} else { // total is not 0 or total is 0, but we are not showing Free label

								regular_total = regular_total * rate; // convert regular cost
								total = total * rate; // convert total cost (sale cost if sale is active on base product or one of components)

								var formatted_regular_total = format_price(regular_total); // format into a nice string
								var formatted_total = format_price(total); // format into a nice string

								if (is_on_sale) { // product is on sale (we need to cross out the regular price and display sale (total) next to it)
									price_html = '<p class="price"><span class="total">' + wc_composite_params.i18n_total + '</span><del>' + formatted_regular_total + '</del> <ins>' + formatted_total + '</ins></p>';
								} else { // not on sale - just display total
									price_html = '<p class="price"><span class="total">' + wc_composite_params.i18n_total + '</span>' + formatted_total + '</p>';
								}

							}

							if (price_html != current_price_html) { // price after conversion is different than it was - it means that component or quantity has been changed
								$(price_selector, this).html(price_html); // let's update the total string
							}

						});
					}

					// Append event listener
					$(observerNode).objectChange(update_totals); // every time observerNode changes, update_totals is called

				});

			}

		})

	}


/* ---------------------------- CART UPDATE ---------------------------- */

// Make sure currency is switched in AJAX cart after user has changed it

	jQuery(document).ready(function ($) {

		if (wcumcs_vars_data.currency_changed == 1 && typeof wc_cart_fragments_params !== 'undefined') { // if currency has been changed AND wc_cart_fragments_params is defined

			/** Cart Handling */
			try {
				$supports_html5_storage = ('sessionStorage' in window && window.sessionStorage !== null);
				window.sessionStorage.setItem('wc', 'test');
				window.sessionStorage.removeItem('wc');
			} catch (err) {
				$supports_html5_storage = false;
			}

			if ($supports_html5_storage != false) {

				$fragment_refresh = {
					url: wc_cart_fragments_params.ajax_url,
					type: 'POST',
					data: { action: 'woocommerce_get_refreshed_fragments' },
					success: function (data) {
						if (data && data.fragments) {

							$.each(data.fragments, function (key, value) {
								$(key).replaceWith(value);
							});

							if ($supports_html5_storage) {
								sessionStorage.setItem(wc_cart_fragments_params.fragment_name, JSON.stringify(data.fragments));
								sessionStorage.setItem('wc_cart_hash', data.cart_hash);
							}

							$('body').trigger('wc_fragments_refreshed');

						}
					}
				};

				$.ajax($fragment_refresh);

			}

		}

	})


/* ---------------------------- PAGE CACHING SUPPORT ---------------------------- */

// Append querystring if page caching support is enabled
// Hugely inspired by WooCommerce ajax geolocation (geolocation.js)

	
	jQuery(function ($) {

		if ($(wcumcs_vars_data.page_cache_support_data).length > 0) { // first check if we are using the page cache support

			var page_cache_data = wcumcs_vars_data.page_cache_support_data;

			var this_page = window.location.toString();

			var $append_hashes = function () {
				if (page_cache_data.hash) {
					$('a[href^="' + page_cache_data.home_url + '"]:not(a[href*="c="]), a[href^="/"]:not(a[href*="c="])').each(function () {
						var $this = $(this);
						var href = $this.attr('href');

						if (href.indexOf('?') > 0) {
							$this.attr('href', href + '&c=' + page_cache_data.hash);
						} else {
							$this.attr('href', href + '?c=' + page_cache_data.hash);
						}
					});
				}
			};

			var $currency_redirect = function (hash) {
				if (this_page.indexOf('?c=') > 0 || this_page.indexOf('&c=') > 0) {
					this_page = this_page.replace(/c=[^&]+/, 'c=' + hash);
				} else if (this_page.indexOf('?') > 0) {
					this_page = this_page + '&c=' + hash;
				} else {
					this_page = this_page + '?c=' + hash;
				}

				window.location = this_page;
			};

			if (
				'1' !== page_cache_data.is_checkout &&
				'1' !== page_cache_data.is_cart &&
				'1' !== page_cache_data.is_account_page &&
				'1' !== page_cache_data.is_customize
			) {
				var post_data = {
					action: 'wcumcs_ajax',
					verification: wcumcs_vars_data.wp_nonce,
					execute: 'get_currency_hash'
				};
				// Send Ajax request:
				jQuery.post(wcumcs_vars_data.ajaxurl, post_data, function (response) {
					if (response.length > 0 && response !== page_cache_data.hash) {
						$currency_redirect(response);
					}
				});

				// Support form elements
				$('form').each(function () {
					var $this = $(this);
					var method = $this.attr('method');

					if (method && 'get' === method.toLowerCase()) {
						$this.append('<input type="hidden" name="c" value="' + page_cache_data.hash + '" />');
					} else {
						var href = $this.attr('action');
						if (href) {
							if (href.indexOf('?') > 0) {
								$this.attr('action', href + '&c=' + page_cache_data.hash);
							} else {
								$this.attr('action', href + '?c=' + page_cache_data.hash);
							}
						}
					}
				});

				// Append hashes on load
				$append_hashes();

			}

			$(document.body).on('added_to_cart', function () {
				$append_hashes();
			});

		}

	});



/* ---------------------------- SWITCHER THEMES ---------------------------- */


/* Regular option-select (dropdown/combobox) */

	jQuery(document).ready(function ($) {

		$("select.wcumcs-select").on('change', function () { // submit post data on dropdown change
			$("#wcumcs-currency-input").val($(this).val()); // assign value from dropdown list to invisible input field
			$("#wcumcs-currency-form").submit(); // and submit it
		})

	})


/* Buttons (<li> items) */

	jQuery(document).ready(function ($) {

		$("a.wcumcs-list-item-link").on('click', function () { // submit post data on link click
			if ($(this).hasClass("selected")) { // if currency is already chosen
				return false; // do nothing
			} else {
				$("#wcumcs-currency-input").val($(this).attr('data-wcumcs-currency')); // assign data attribute from clicked link to invisible input field
				$("#wcumcs-currency-form").submit(); // and submit it
			}
		})

	})


