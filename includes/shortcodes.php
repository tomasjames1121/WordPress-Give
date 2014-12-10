<?php
/**
 * Shortcodes
 *
 * @package     Give
 * @subpackage  Shortcodes
 * @copyright   Copyright (c) 2014, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Donation History Shortcode
 *
 * Displays a user's donation history.
 *
 * @since 1.0
 * @return string
 */
function give_download_history() {
	if ( is_user_logged_in() ) {
		ob_start();
		give_get_template_part( 'history', 'downloads' );
		return ob_get_clean();
	}
}

add_shortcode( 'download_history', 'give_download_history' );

/**
 * Purchase History Shortcode
 *
 * Displays a user's purchase history.
 *
 * @since 1.0
 * @return string
 */
function give_purchase_history() {
	if ( is_user_logged_in() ) {
		ob_start();
		give_get_template_part( 'history', 'purchases' );

		return ob_get_clean();
	}
}

add_shortcode( 'purchase_history', 'give_purchase_history' );

/**
 * Checkout Form Shortcode
 *
 * Show the checkout form.
 *
 * @since 1.0
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string
 */
function give_checkout_form_shortcode( $atts, $content = null ) {
	return give_checkout_form();
}

add_shortcode( 'download_checkout', 'give_checkout_form_shortcode' );

/**
 * Download Cart Shortcode
 *
 * Show the shopping cart.
 *
 * @since 1.0
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string
 */
function give_cart_shortcode( $atts, $content = null ) {
	return give_shopping_cart();
}

add_shortcode( 'download_cart', 'give_cart_shortcode' );

/**
 * Login Shortcode
 *
 * Shows a login form allowing users to users to log in. This function simply
 * calls the give_login_form function to display the login form.
 *
 * @since 1.0
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @uses  give_login_form()
 * @return string
 */
function give_login_form_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'redirect' => '',
		), $atts, 'give_login' )
	);

	return give_login_form( $redirect );
}

add_shortcode( 'give_login', 'give_login_form_shortcode' );

/**
 * Register Shortcode
 *
 * Shows a registration form allowing users to users to register for the site
 *
 * @since 2.0
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @uses  give_register_form()
 * @return string
 */
function give_register_form_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'redirect' => '',
		), $atts, 'give_register' )
	);

	return give_register_form( $redirect );
}

add_shortcode( 'give_register', 'give_register_form_shortcode' );

/**
 * Discounts short code
 *
 * Displays a list of all the active discounts. The active discounts can be configured
 * from the Discount Codes admin screen.
 *
 * @since 1.0.8.2
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @uses  give_get_discounts()
 * @return string $discounts_lists List of all the active discount codes
 */
function give_discounts_shortcode( $atts, $content = null ) {
	$discounts = give_get_discounts();

	$discounts_list = '<ul id="give_discounts_list">';

	if ( ! empty( $discounts ) && give_has_active_discounts() ) {

		foreach ( $discounts as $discount ) {

			if ( give_is_discount_active( $discount->ID ) ) {

				$discounts_list .= '<li class="give_discount">';

				$discounts_list .= '<span class="give_discount_name">' . give_get_discount_code( $discount->ID ) . '</span>';
				$discounts_list .= '<span class="give_discount_separator"> - </span>';
				$discounts_list .= '<span class="give_discount_amount">' . give_format_discount_rate( give_get_discount_type( $discount->ID ), give_get_discount_amount( $discount->ID ) ) . '</span>';

				$discounts_list .= '</li>';

			}

		}

	} else {
		$discounts_list .= '<li class="give_discount">' . __( 'No discounts found', 'edd' ) . '</li>';
	}

	$discounts_list .= '</ul>';

	return $discounts_list;
}

add_shortcode( 'download_discounts', 'give_discounts_shortcode' );

/**
 * Purchase Collection Shortcode
 *
 * Displays a collection purchase link for adding all items in a taxonomy term
 * to the cart.
 *
 * @since 1.0.6
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string
 */
function give_purchase_collection_shortcode( $atts, $content = null ) {
	global $give_options;

	extract( shortcode_atts( array(
			'taxonomy' => '',
			'terms'    => '',
			'text'     => __( 'Purchase All Items', 'edd' ),
			'style'    => isset( $give_options['button_style'] ) ? $give_options['button_style'] : 'button',
			'color'    => isset( $give_options['checkout_color'] ) ? $give_options['checkout_color'] : 'blue',
			'class'    => 'edd-submit'
		), $atts, 'purchase_collection' )
	);

	$button_display = implode( ' ', array( $style, $color, $class ) );

	return '<a href="' . add_query_arg( array(
			'give_action' => 'purchase_collection',
			'taxonomy'   => $taxonomy,
			'terms'      => $terms
		) ) . '" class="' . $button_display . '">' . $text . '</a>';
}

add_shortcode( 'purchase_collection', 'give_purchase_collection_shortcode' );

/**
 * Downloads Shortcode
 *
 * This shortcodes uses the WordPress Query API to get downloads with the
 * arguments specified when using the shortcode. A list of the arguments
 * can be found from the EDD Dccumentation. The shortcode will take all the
 * parameters and display the downloads queried in a valid HTML <div> tags.
 *
 * @since    1.0.6
 * @internal Incomplete shortcode
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string $display Output generated from the downloads queried
 */
function give_downloads_query( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'category'         => '',
		'exclude_category' => '',
		'tags'             => '',
		'exclude_tags'     => '',
		'relation'         => 'AND',
		'number'           => 9,
		'price'            => 'no',
		'excerpt'          => 'yes',
		'full_content'     => 'no',
		'buy_button'       => 'yes',
		'columns'          => 3,
		'thumbnails'       => 'true',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'ids'              => ''
	), $atts, 'downloads' );

	$query = array(
		'post_type'      => 'download',
		'posts_per_page' => (int) $atts['number'],
		'orderby'        => $atts['orderby'],
		'order'          => $atts['order']
	);

	if ( $query['posts_per_page'] < - 1 ) {
		$query['posts_per_page'] = abs( $query['posts_per_page'] );
	}

	switch ( $atts['orderby'] ) {
		case 'price':
			$atts['orderby']   = 'meta_value';
			$query['meta_key'] = 'give_price';
			$query['orderby']  = 'meta_value_num';
			break;

		case 'title':
			$query['orderby'] = 'title';
			break;

		case 'id':
			$query['orderby'] = 'ID';
			break;

		case 'random':
			$query['orderby'] = 'rand';
			break;

		default:
			$query['orderby'] = 'post_date';
			break;
	}

	if ( $atts['tags'] || $atts['category'] || $atts['exclude_category'] || $atts['exclude_tags'] ) {

		$query['tax_query'] = array(
			'relation' => $atts['relation']
		);

		if ( $atts['tags'] ) {
			$tag_list  = explode( ',', $atts['tags'] );
			$_tax_tags = array();

			foreach ( $tag_list as $tag ) {
				if ( is_numeric( $tag ) ) {
					$term_id = $tag;
				} else {
					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if ( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$_tax_tags[] = $term_id;
			}

			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'field'    => 'term_id',
				'terms'    => $_tax_tags
			);
		}

		if ( $atts['category'] ) {
			$categories = explode( ',', $atts['category'] );
			$_tax_cats  = array();

			foreach ( $categories as $category ) {
				if ( is_numeric( $category ) ) {
					$term_id = $category;
				} else {
					$term = get_term_by( 'slug', $category, 'download_category' );

					if ( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$_tax_cats[] = $term_id;
			}

			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'field'    => 'term_id',
				'terms'    => $_tax_cats
			);
		}

		if ( $atts['exclude_category'] ) {
			$categories = explode( ',', $atts['exclude_category'] );
			$_tax_cats  = array();

			foreach ( $categories as $category ) {
				if ( is_numeric( $category ) ) {
					$term_id = $category;
				} else {
					$term = get_term_by( 'slug', $category, 'download_category' );

					if ( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$_tax_cats[] = $term_id;
			}

			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'field'    => 'term_id',
				'terms'    => $_tax_cats,
				'operator' => 'NOT IN'
			);
		}

		if ( $atts['exclude_tags'] ) {
			$tag_list  = explode( ',', $atts['exclude_tags'] );
			$_tax_tags = array();

			foreach ( $tag_list as $tag ) {
				if ( is_numeric( $tag ) ) {
					$term_id = $tag;
				} else {
					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if ( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$_tax_tags[] = $term_id;
			}

			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'field'    => 'term_id',
				'terms'    => $_tax_tags,
				'operator' => 'NOT IN'
			);
		}
	}

	if ( ! empty( $atts['ids'] ) ) {
		$query['post__in'] = explode( ',', $atts['ids'] );
	}

	if ( get_query_var( 'paged' ) ) {
		$query['paged'] = get_query_var( 'paged' );
	} else if ( get_query_var( 'page' ) ) {
		$query['paged'] = get_query_var( 'page' );
	} else {
		$query['paged'] = 1;
	}

	switch ( intval( $atts['columns'] ) ) :
		case 0:
			$column_width = 'inherit';
			break;
		case 1:
			$column_width = '100%';
			break;
		case 2:
			$column_width = '50%';
			break;
		case 3:
		default:
			$column_width = '33%';
			break;
		case 4:
			$column_width = '25%';
			break;
		case 5:
			$column_width = '20%';
			break;
		case 6:
			$column_width = '16.6%';
			break;
	endswitch;

	// Allow the query to be manipulated by other plugins
	$query = apply_filters( 'give_downloads_query', $query, $atts );

	$downloads = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i             = 1;
		$wrapper_class = 'give_download_columns_' . $atts['columns'];
		ob_start(); ?>
		<div class="give_downloads_list <?php echo apply_filters( 'give_downloads_list_wrapper_class', $wrapper_class, $atts ); ?>">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post(); ?>
				<div itemscope itemtype="http://schema.org/Product" class="<?php echo apply_filters( 'give_download_class', 'give_download', get_the_ID(), $atts, $i ); ?>" id="give_download_<?php echo get_the_ID(); ?>" style="width: <?php echo $column_width; ?>; float: left;">
					<div class="give_download_inner">
						<?php

						do_action( 'give_download_before' );

						if ( 'false' != $atts['thumbnails'] ) :
							give_get_template_part( 'shortcode', 'content-image' );
						endif;

						give_get_template_part( 'shortcode', 'content-title' );

						if ( $atts['excerpt'] == 'yes' && $atts['full_content'] != 'yes' ) {
							give_get_template_part( 'shortcode', 'content-excerpt' );
						} else if ( $atts['full_content'] == 'yes' ) {
							give_get_template_part( 'shortcode', 'content-full' );
						}

						if ( $atts['price'] == 'yes' ) {
							give_get_template_part( 'shortcode', 'content-price' );
						}

						if ( $atts['buy_button'] == 'yes' ) {
							give_get_template_part( 'shortcode', 'content-cart-button' );
						}

						do_action( 'give_download_after' );

						?>
					</div>
				</div>
				<?php if ( $atts['columns'] != 0 && $i % $atts['columns'] == 0 ) { ?>
					<div style="clear:both;"></div><?php } ?>
				<?php $i ++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>

			<div id="give_download_pagination" class="navigation">
				<?php
				if ( is_single() ) {
					echo paginate_links( apply_filters( 'give_download_pagination_args', array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				} else {
					$big = 999999;
					echo paginate_links( apply_filters( 'give_download_pagination_args', array(
						'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				}
				?>
			</div>

		</div>
		<?php
		$display = ob_get_clean();
	else:
		$display = sprintf( _x( 'No %s found', 'download post type name', 'edd' ), give_get_label_plural() );
	endif;

	return apply_filters( 'downloads_shortcode', $display, $atts, $atts['buy_button'], $atts['columns'], $column_width, $downloads, $atts['excerpt'], $atts['full_content'], $atts['price'], $atts['thumbnails'], $query );
}

add_shortcode( 'downloads', 'give_downloads_query' );

/**
 * Price Shortcode
 *
 * Shows the price of a download.
 *
 * @since 1.1.3.3
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string
 */
function give_download_price_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'id'       => null,
			'price_id' => false,
		), $atts, 'give_price' )
	);

	if ( is_null( $id ) ) {
		$id = get_the_ID();
	}

	return give_price( $id, false, $price_id );
}

add_shortcode( 'give_price', 'give_download_price_shortcode' );

/**
 * Receipt Shortcode
 *
 * Shows an order receipt.
 *
 * @since 1.4
 *
 * @param array  $atts Shortcode attributes
 * @param string $content
 *
 * @return string
 */
function give_receipt_shortcode( $atts, $content = null ) {
	global $give_receipt_args;

	$give_receipt_args = shortcode_atts( array(
		'error'          => __( 'Sorry, trouble retrieving payment receipt.', 'edd' ),
		'price'          => true,
		'discount'       => true,
		'products'       => true,
		'date'           => true,
		'notes'          => true,
		'payment_key'    => false,
		'payment_method' => true,
		'payment_id'     => true
	), $atts, 'give_receipt' );

	$session = give_get_purchase_session();
	if ( isset( $_GET['payment_key'] ) ) {
		$payment_key = urldecode( $_GET['payment_key'] );
	} elseif ( $give_receipt_args['payment_key'] ) {
		$payment_key = $give_receipt_args['payment_key'];
	} else if ( $session ) {
		$payment_key = $session['purchase_key'];
	}

	// No key found
	if ( ! isset( $payment_key ) ) {
		return $give_receipt_args['error'];
	}

	$give_receipt_args['id'] = give_get_purchase_id_by_key( $payment_key );
	$customer_id            = give_get_payment_user_id( $give_receipt_args['id'] );

	/*
	 * Check if the user has permission to view the receipt
	 *
	 * If user is logged in, user ID is compared to user ID of ID stored in payment meta
	 *
	 * Or if user is logged out and purchase was made as a guest, the purchase session is checked for
	 *
	 * Or if user is logged in and the user can view sensitive shop data
	 *
	 */

	$user_can_view = ( is_user_logged_in() && $customer_id == get_current_user_id() ) || ( ( $customer_id == 0 || $customer_id == '-1' ) && ! is_user_logged_in() && give_get_purchase_session() ) || current_user_can( 'view_shop_sensitive_data' );

	if ( ! apply_filters( 'give_user_can_view_receipt', $user_can_view, $give_receipt_args ) ) {
		return $give_receipt_args['error'];
	}

	ob_start();

	give_get_template_part( 'shortcode', 'receipt' );

	$display = ob_get_clean();

	return $display;
}

add_shortcode( 'give_receipt', 'give_receipt_shortcode' );

/**
 * Profile Editor Shortcode
 *
 * Outputs the EDD Profile Editor to allow users to amend their details from the
 * front-end. This function uses the EDD templating system allowing users to
 * override the default profile editor template. The profile editor template is located
 * under templates/profile-editor.php, however, it can be altered by creating a
 * file called profile-editor.php in the give_template directory in your active theme's
 * folder. Please visit the EDD Documentation for more information on how the
 * templating system is used.
 *
 * @since  1.4
 *
 * @author Sunny Ratilal
 *
 * @param      $atts Shortcode attributes
 * @param null $content
 *
 * @return string Output generated from the profile editor
 */
function give_profile_editor_shortcode( $atts, $content = null ) {
	ob_start();

	give_get_template_part( 'shortcode', 'profile-editor' );

	$display = ob_get_clean();

	return $display;
}

add_shortcode( 'give_profile_editor', 'give_profile_editor_shortcode' );

/**
 * Process Profile Updater Form
 *
 * Processes the profile updater form by updating the necessary fields
 *
 * @since  1.4
 * @author Sunny Ratilal
 *
 * @param array $data Data sent from the profile editor
 *
 * @return void
 */
function give_process_profile_editor_updates( $data ) {
	// Profile field change request
	if ( empty( $_POST['give_profile_editor_submit'] ) && ! is_user_logged_in() ) {
		return false;
	}

	// Nonce security
	if ( ! wp_verify_nonce( $data['give_profile_editor_nonce'], 'edd-profile-editor-nonce' ) ) {
		return false;
	}

	$user_id       = get_current_user_id();
	$old_user_data = get_userdata( $user_id );

	$display_name = isset( $data['give_display_name'] ) ? sanitize_text_field( $data['give_display_name'] ) : $old_user_data->display_name;
	$first_name   = isset( $data['give_first_name'] ) ? sanitize_text_field( $data['give_first_name'] ) : $old_user_data->first_name;
	$last_name    = isset( $data['give_last_name'] ) ? sanitize_text_field( $data['give_last_name'] ) : $old_user_data->last_name;
	$email        = isset( $data['give_email'] ) ? sanitize_email( $data['give_email'] ) : $old_user_data->user_email;
	$line1        = ( isset( $data['give_address_line1'] ) ? sanitize_text_field( $data['give_address_line1'] ) : '' );
	$line2        = ( isset( $data['give_address_line2'] ) ? sanitize_text_field( $data['give_address_line2'] ) : '' );
	$city         = ( isset( $data['give_address_city'] ) ? sanitize_text_field( $data['give_address_city'] ) : '' );
	$state        = ( isset( $data['give_address_state'] ) ? sanitize_text_field( $data['give_address_state'] ) : '' );
	$zip          = ( isset( $data['give_address_zip'] ) ? sanitize_text_field( $data['give_address_zip'] ) : '' );
	$country      = ( isset( $data['give_address_country'] ) ? sanitize_text_field( $data['give_address_country'] ) : '' );

	$userdata = array(
		'ID'           => $user_id,
		'first_name'   => $first_name,
		'last_name'    => $last_name,
		'display_name' => $display_name,
		'user_email'   => $email
	);


	$address = array(
		'line1'   => $line1,
		'line2'   => $line2,
		'city'    => $city,
		'state'   => $state,
		'zip'     => $zip,
		'country' => $country
	);

	do_action( 'give_pre_update_user_profile', $user_id, $userdata );

	// New password
	if ( ! empty( $data['give_new_user_pass1'] ) ) {
		if ( $data['give_new_user_pass1'] !== $data['give_new_user_pass2'] ) {
			give_set_error( 'password_mismatch', __( 'The passwords you entered do not match. Please try again.', 'edd' ) );
		} else {
			$userdata['user_pass'] = $data['give_new_user_pass1'];
		}
	}

	// Make sure the new email doesn't belong to another user
	if ( $email != $old_user_data->user_email ) {
		if ( email_exists( $email ) ) {
			give_set_error( 'email_exists', __( 'The email you entered belongs to another user. Please use another.', 'edd' ) );
		}
	}

	// Check for errors
	$errors = give_get_errors();

	if ( $errors ) {
		// Send back to the profile editor if there are errors
		wp_redirect( $data['give_redirect'] );
		give_die();
	}

	// Update the user
	$meta    = update_user_meta( $user_id, '_give_user_address', $address );
	$updated = wp_update_user( $userdata );

	if ( $updated ) {
		do_action( 'give_user_profile_updated', $user_id, $userdata );
		wp_redirect( add_query_arg( 'updated', 'true', $data['give_redirect'] ) );
		give_die();
	}
}

add_action( 'give_edit_user_profile', 'give_process_profile_editor_updates' );
