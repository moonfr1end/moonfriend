$(document).ready(function() {
	$('#close-one-click-form').click(function() {
		$('.one-click-block').css("display", "none");
	})

	$('#display-one-click').click(function() {
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

	$('#send-order-one-click').click(function() {
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
				if(data == 4) {
					var res = JSON.parse(data);
					$('#soobwenie').html(res);
					$('#name-oc').val('');
					$('#phone-oc').val('');
					$('#email-oc').val('');
					$('.oc-notification').css('display', 'block');
					$('.one-click-block').css('display', 'none');
					$('#oc-error').html('');
				}
				else if(data == 0) {
					$('#oc-error').html('Заполните форму');
				}
				else if(data == 1) {
					$('#oc-error').html('Сначала авторизируйтесь');
				}
				else if(data == 2) {
					$('#oc-error').html('Почта неправильна');
				}
				else if(data == 3) {
					$('#oc-error').html('Имя неправильно');
				}
			}
		});
	})

	$('#oc-close-msg').click(function() {
		$('.one-click-block').css('display', 'none');
		$('.oc-notification').css('display', 'none');
	})
});