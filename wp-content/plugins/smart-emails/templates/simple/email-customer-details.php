<?php
/**
 * Additional Customer Details
 *
 * This is extra customer data which can be filtered by plugins. It outputs below the order item table.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-customer-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates/Emails
 * @version     2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<table id="customer_details">
	<tr>
		<td>
			<h3 style="padding-bottom:10px;"><?php esc_html_e( 'Customer details', 'smart-emails' ); ?></h3>
			<?php foreach ( $fields as $field ) : ?>

				<strong><?php echo wp_kses_post( $field['label'] ); ?>:</strong> <span class="text"><?php echo wp_kses_post( $field['value'] ); ?></span><br>
			<?php endforeach; ?>
		</td>
	</tr>
</table>
<?php
