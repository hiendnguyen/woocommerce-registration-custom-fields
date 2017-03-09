<?php
/* Plugin Name: WooCommerce Registration Custom Fields
* Plugin URI: https://vndeveloper.com
* Description: Add Fist Name, Last Name, Phone, Company, Role, Privacy Policy and Terms of Use into Registration form.
* Version: 1.0.0
* Author: VNDeveloper
* Author URI: https://vndeveloper.com
* Requires at least: 4.1
* Tested up to: 4.7
* Text Domain: vndev-wooc-signup
* Domain Path: /languages/
* License: GPL2+
*/

if(!defined('ABSPATH')) {
    exit;
}

class VNDeveloper_Wooc_Registration {

    public function __construct() {
        $this->includes();
        $this->init();        

        if(is_admin()) {
        }
    }

    private function includes() {
        include( 'includes/registration-form.php' );
    }

    public function init() {
    }
}

new VNDeveloper_Wooc_Registration();