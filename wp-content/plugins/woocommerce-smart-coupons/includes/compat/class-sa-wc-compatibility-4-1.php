<?php
/**
 * Compatibility class for WooCommerce 4.1.0
 *
 * @category    Class
 * @package     WC-compat
 * @author      StoreApps
 * @version     1.0.0
 * @since       WooCommerce 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_4_1' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 4.1.0
	 */
	class SA_WC_Compatibility_4_1 extends SA_WC_Compatibility_4_0 {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 4.1.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_41() {
			return self::is_wc_greater_than( '4.0.1' );
		}

	}

}
