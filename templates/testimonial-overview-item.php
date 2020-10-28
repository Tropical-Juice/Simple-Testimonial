	<div class="testminials--list__single">
		<p class="testimonials--item__meta"><?php echo $data['name'][0]; ?> (<?php echo $data['function'][0]; ?>) <?php _e('at', TROPICAL_TESTIMONIALS_TEXT_DOMAIN)?> <?php echo $data['organization'][0]; ?></p>
		<p class="testimonials--item__body">"<?php echo $data['testimonial_text'][0]; ?>" - <?php printf(__('Gives a score of %1$s out of %2$s', TROPICAL_TESTIMONIALS_TEXT_DOMAIN), $data['rating'][0], 5);?> </p>
	</div>