<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  ?>
	<div class="testimonial" dir="ltr" lang="nl-NL" role="form" style="max-width:400px;">
		<h1>Vul hier uw beoordeling in</h1>
		<form class="testimonial-form" enctype="multipart/form-data" id="testimonial_form" method="post" name="testimonial_form" novalidate="novalidate">
			<label><?php _e('Company', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></label><br>
			<input type="text" name="organization"><br>
			<label><?php _e('Your name', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></label><br>
			<input type="text" name="name"><br>
			<label><?php _e('Your position', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></label><br>
			<input type="text" name="function"><br>
			<label><?php _e('Your rating', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></label><br>
			<div class="rating-container">
				<div id="rateYo"></div>
				<div class="counter"></div>
				<input type="hidden" id="input-rating" name="rating"><br>
			</div>
			<label><?php _e('Your testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></label><br>
			<textarea name="testimonial_text" placeholder="<?php _e('Please enter your testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?>"></textarea><br>
			<button class="wpcf7-form-control wpcf7-submit" name="testimonial" type="submit"><?php _e('Add testimonial', TROPICAL_TESTIMONIALS_TEXT_DOMAIN) ?></button>
		</form>
	</div>