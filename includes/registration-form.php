<?php 

/*** Change default login url to WooCommerce My Account ***/
function my_login_page( $login_url, $redirect ) {
    $myaccount_url = get_permalink(wc_get_page_id('myaccount'));
    return $redirect == '' ? $myaccount_url : $myaccount_url . '?redirect_to=' . $redirect;
}
add_filter( 'login_url', 'my_login_page', 10, 2 );

/*** We don't want "account page" displays after login by default. We want to display homepage instead ***/
function wooc_user_redirect( $redirect, $user ) {
	if(isset($_GET['redirect_to'])){
		$redirect = $_GET['redirect_to'];	
	} else {
		$redirect = home_url();
	}
	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wooc_user_redirect', 10, 2 );

/** Add more fields into registration form*/
function wooc_extra_register_fields() {
	?>

	<p class="form-row form-row-first">
	<label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
	</p>

	<p class="form-row form-row-last">
	<label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
	<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
	</p>
	
	<p class="form-row form-row-wide">
	<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?></label>
	<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone']); ?>" />
	</p>

	<p class="form-row form-row-wide">
	<label for="billing_company"><?php _e( 'Company', 'woocommerce' ); ?></label>
	<input type="text" class="input-text" name="billing_company" id="billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company']); ?>" />
	</p>

	<p class="form-row form-row-wide">
	<label for="user_role">Register as</label>
	<select id="user_role" name="user_role" style="height: 34px;">
	<option value="subscriber">Subscriber</option>
	<option value="contributor">Contributor</option>
	<select>
	</p>

	<div class="clear"></div>
	
	<?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

function wooc_subscribe_to_newsletter(){
	?>
	<p class="form-row form-row-wide" id="subscribe_to_newsletter_field"><label class="checkbox ">
	<input type="checkbox" class="input-checkbox " name="subscribe_to_newsletter" id="subscribe_to_newsletter" value="1" checked="checked"> Subscribe to our newsletter?</label></p>
	<?php
}
add_action( 'woocommerce_register_form', 'wooc_subscribe_to_newsletter' );

/** Validate the extra register fields. */
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
		$validation_errors->add( 'billing_first_name_error', __( 'What\'s your first name?', 'woocommerce' ) );
	}
	
	if (  isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		$validation_errors->add( 'billing_last_name_error', __( 'What\'s your last name?', 'woocommerce' ) );
	}
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );

/** Save the extra register fields. */
function wooc_save_extra_register_fields( $customer_id ) {
	if ( isset( $_POST['billing_first_name'] ) ) {
		// WordPress default first name field.
		update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		// WooCommerce billing first name.
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
	}
	
	if ( isset( $_POST['billing_last_name'] ) ) {
		// WordPress default last name field.
		update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		// WooCommerce billing last name.
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
	}
	
	if ( isset( $_POST['billing_phone'] ) ) {
		update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
	}
	
	if ( isset( $_POST['billing_company'] ) ) {
		update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
	}
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
/** DISABLE THE FIRST NAME AND LAST NAMES CREATED BY PLUGIN WOOCOMMERCE SIMPLE REGISTRATION, BECAUSE THEY APPEAR IN ENGLISH ONLY, NOT BEING TRANSLATED. */
add_filter( 'woocommerce_simple_registration_name_fields', '__return_false' );

/** Assign user role */
function wooc_assign_user_role($customer_data){
    if ( isset( $_POST['user_role'] ) ) {
		$customer_data['role'] = sanitize_text_field($_POST['user_role']);
    }
    return $customer_data;
}
add_filter( 'woocommerce_new_customer_data', 'wooc_assign_user_role');

/** Set default display name */
function default_display_name($name) {
	if ( isset( $_POST['billing_first_name'] ) ) {
		$firstname = sanitize_text_field( $_POST['billing_first_name'] );
	}
	
	if ( isset( $_POST['billing_last_name'] ) ) {
		$lastname = sanitize_text_field( $_POST['billing_last_name'] );
	}
	$name = $firstname . ' ' . $lastname;
	
	return $name;
}
add_filter('pre_user_display_name','default_display_name');

/** Add agree to terms and conditions for WooCommerce registration. */
function wooc_agree_terms_conditions(){
	?>
	<p>By clicking on "REGISTER", I have read and accepted <b><a href="/privacy/" target="_blank">Privacy Policy</a> and <b><a href="/terms/" target="_blank">Terms of Use</a>.</p>
	<?php
}
add_action( 'woocommerce_register_form_end', 'wooc_agree_terms_conditions');

/** Prevent automatic login by logoff and show tooltip reminding user to check their mailbox for login credentials. */
function wooc_registration_redirect( $redirect_to ) {
	$current_user = wp_get_current_user();
	wp_logout();
	wp_redirect( $myaccount_url = get_permalink(wc_get_page_id('myaccount')) . '?context=RegistrationSucceeded&email=' . $current_user->user_email);
	exit;
}
add_filter('woocommerce_registration_redirect', 'wooc_registration_redirect');

/*** Notify user on WooCommerce Registration ***/
function wooc_init(){
    if(isset($_GET['context']) && $_GET['context'] == 'RegistrationSucceeded'){
		if(isset($_GET['email'])){
			wc_add_notice( __( 'We’ve sent a message to ' . $_GET['email'] . '. Open it up for Login Credentials. We’ll take it from there.', 'inkfool' ) );
		}
		else{
			wc_add_notice( __( 'We’ve sent a message to you. Open it up for Login Credentials. We’ll take it from there.', 'inkfool' ) );
		}
    }
}
add_action( 'init', 'wooc_init' );

/*** Notify admin on WooCommerce User Registration ***/
function admin_email_on_registration( $customer_id) {
	wp_new_user_notification( $customer_id );
}
add_action('woocommerce_created_customer', 'admin_email_on_registration', 10 , 1);

/*** Remove WooCommerce Password Strength Meter ***/
function wc_ninja_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action( 'wp_print_scripts', 'wc_ninja_remove_password_strength', 100 );
