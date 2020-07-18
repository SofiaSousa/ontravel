<?php
/**
 * Display single product reviews (comments)
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments">
		<h2 class="woocommerce-Reviews-title"><?php
			if ( wc_review_ratings_enabled() && ( $count = $product->get_review_count() ) ) {
				/* translators: 1: reviews count 2: product name */
				printf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'citytours' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
			} else {
				_e( 'Reviews', 'citytours' );
			}
		?></h2>

		<?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', 'citytours' ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

		<hr>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => have_comments() ? __( 'Add a review', 'citytours' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'citytours' ), get_the_title() ),
						'title_reply_to'       => __( 'Leave a Reply to %s', 'citytours' ),
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'citytours' ) . ' <span class="required">*</span></label> ' .
										'<input id="author" name="author" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required /></p>',
							'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'citytours' ) . ' <span class="required">*</span></label> ' .
										'<input id="email" name="email" type="email" class="form-control" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required /></p>',
						),
						'submit_button'	=> '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
						'label_submit'  => __( 'Add Review', 'citytours' ),
						'class_submit'	=> 'btn_1',
						'logged_in_as'  => '',
						'comment_field' => '',
					);

					if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
						$comment_form['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'citytours' ), esc_url( $account_page_url ) ) . '</p>';
					}

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your rating', 'citytours' ) .'</label><select name="rating" id="rating" aria-required="true" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'citytours' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'citytours' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'citytours' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'citytours' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'citytours' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'citytours' ) . '</option>
						</select></p>';
					}

					$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'citytours' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" class="form-control" cols="45" rows="8" aria-required="true" required></textarea></p>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'citytours' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>
