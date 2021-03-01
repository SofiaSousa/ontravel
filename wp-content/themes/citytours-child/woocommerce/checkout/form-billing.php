<?php
/**
 * Checkout billing information form
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */

?>
<div class="woocommerce-billing-fields row">
    <div class="col-sm-12">
        <div class="default-title">
            <?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

                <h2><?php _e( 'Billing &amp; Shipping', 'citytours' ); ?></h2>

            <?php else : ?>

                <h2><?php _e( 'Customer Details', 'citytours' ); ?></h2>

            <?php endif; ?>
        </div>
    </div>

    <?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

    <div class="woocommerce-billing-fields__field-wrapper">

        <?php
            $fields = $checkout->get_checkout_fields( 'billing' );

            foreach ( $fields as $key => $field ) {
                if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
                    $field['country'] = $checkout->get_value( $field['country_field'] );
                }
                woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
            }
        ?>

    </div>

    <?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

    <?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>

        <?php if ( ! $checkout->is_registration_required() ) : ?>

            <p class="form-row form-row-wide create-account check-box form-group col-sm-12">
                <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'citytours' ); ?></label>
            </p>

        <?php endif; ?>

        <?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

        <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

            <div class="create-account col-sm-12">

                <p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'citytours' ); ?></p>

                <?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>

                    <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

                <?php endforeach; ?>

                <div class="clear"></div>

            </div>

        <?php endif; ?>

        <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

    <?php endif; ?>
</div>
