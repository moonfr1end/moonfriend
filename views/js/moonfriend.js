$(document).ready(function() {
	$('body').on('click', '#close-one-click-form', function() {
		$('.one-click-block').css("display", "none");
	})

	$('body').on('click', '#display-one-click', function() {
		$.ajax({
			url: mf_ajax + '?action=displayForm',
			data: {
				id_product: $('#product_page_product_id').val()
			},
			method: 'POST',
			success: function(data) {
				var results = JSON.parse(data);
				$('.oc-product-name').html(results['name']);
				$('.oc-product-price').html(results['price']+' ₽');
				$('#oc-img').children('img').attr('src', 'http://'+results['image']);
			}
		});

		$('.one-click-block').css("display", "block");
	})

	$('body').on('click', '#send-order-one-click', function() {
		$.ajax({
			url: mf_ajax + '?action=sendForm',
			data: {
				id_product: $('#product_page_product_id').val(),
				name: $('#name-oc').val(),
				phone: $('#phone-oc').val(),
				email: $('#email-oc').val()
			},
			method: 'POST',
			success: function(data) {
				if(data == true) {
					$('#name-oc').val('');
					$('#phone-oc').val('');
					$('#email-oc').val('');
					$('.oc-notification').css('display', 'block');
					$('.one-click-block').css('display', 'none');
					$('#oc-error').html('');
				} else {
					var res = JSON.parse(data);
					$('#oc-error').html(res[0]);
				}
			}
		});
	})

	$('body').on('click', '#oc-close-msg', function() {
		$('.one-click-block').css('display', 'none');
		$('.oc-notification').css('display', 'none');
	})
});