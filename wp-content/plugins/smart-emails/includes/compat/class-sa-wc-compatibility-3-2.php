<?php
/**
 * Compatibility class for WooCommerce 3.2.0
 *
 * @package     WC-compat
 * @author      StoreApps
 * @version     1.0.0
 * @since       WooCommerce 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_3_2' ) ) {

	/**
	 * Class to check WooCommerce version is greater than and equal to 3.2.0
	 */
	class SA_WC_Compatibility_3_2 extends SA_WC_Compatibility_3_1 {

		/**
		 * Function to check if WooCommerce is Greater Than And Equal To 3.2.0
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_32() {
			return self::is_wc_greater_than( '3.1.2' );
		}

	}

}
