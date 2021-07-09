<button type="button" id="display-one-click" class="btn btn-primary">
    {l s='Купить в один клик' mod='moonfriend'}
</button>

<div class="one-click-block">
    <div class="modal-content" style="position: fixed; top: 0px; left: 50%; transform: translateX(-50%); width: 80%; z-index: 10">
        <div class="modal-header">
            <button type="button" id="close-one-click-form" class="close" aria-label="Закрыть">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 col-sm-6 hidden-xs-down">
                    <div class="images-container">
                        <div class="product-cover" id="oc-img">
                            <h4 class="h4 oc-product-name">Name</h4>
                            <img class="js-qv-product-cover" src="" style="width:100%;" itemprop="image">
                            <!-- <img class="js-qv-product-cover" src="http://prestashop/21-large_default/brown-bear-printed-sweater.jpg" alt="Brown bear printed sweater" title="Brown bear printed sweater" style="width:100%;" itemprop="image"> -->
                            <h4 class="oc-product-price">22,94 ₽</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <h1 style="text-align: center" class="h1">Форма заказа</h1>
                    <form method="post" class="one-click-form">
                        <div class="txt_field">
                            <input id="name-oc" type="text" required>
                            <span></span>
                            <label>Имя</label>
                        </div>
                        <div class="txt_field">
                            <input id="phone-oc" type="number" required>
                            <span></span>
                            <label>Телефон</label>
                        </div>
                        <div class="txt_field">
                            <input id="email-oc" type="text" required>
                            <span></span>
                            <label>E-Mail</label>
                        </div>
                        <input type="button" id="send-order-one-click" value="Заказать">
                        <h4 class="h4" id="oc-error"></h4>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="oc-notification">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="h4" style="text-align: center">Заказ в один клик</h4>
        </div>
        <div class="modal-body">
            <h4 class="h4" style="text-align: center">{$MSG}</h4>
            <!-- <div id="soobwenie"></div> -->
        </div>
        <div class="modal-footer">
            <button id="oc-close-msg" type="button" class="btn-primary">Закрыть</button>
        </div>
    </div>
</div>

<script>
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
</script>

