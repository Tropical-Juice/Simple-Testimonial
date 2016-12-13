jQuery(function () {
	jQuery("#rateYo").rateYo({
		rating: 3.6
	});
});

jQuery(function () {
	jQuery("#rateYo").rateYo()
    	.on("rateyo.change", function (e, data) {
		var rating = data.rating;
		jQuery(this).next().text(rating).next().val(rating);
		console.log(rating);
    });
});