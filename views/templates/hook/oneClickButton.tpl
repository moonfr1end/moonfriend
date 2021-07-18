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
                            <h4 class="h4 oc-product-name"></h4>
                            <img class="js-qv-product-cover" src="" style="width:100%;" itemprop="image">
                            <h4 class="oc-product-price"></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <h1 style="text-align: center" class="h1">Форма заказа</h1>
                    <form method="post" class="one-click-form">
                        <div class="txt_field">
                            <input id="name-oc" value="{$NAME}" type="text" required>
                            <span></span>
                            <label>{l s='Имя' mod='moonfriend'}</label>
                        </div>
                        <div class="txt_field">
                            <input id="phone-oc" value="{$PHONE}" type="number" required>
                            <span></span>
                            <label>{l s='Телефон' mod='moonfriend'}</label>
                        </div>
                        <div class="txt_field">
                            <input id="email-oc" value="{$EMAIL}" type="text" required>
                            <span></span>
                            <label>{l s='E-Mail' mod='moonfriend'}</label>
                        </div>
                        <input type="button" id="send-order-one-click" value="Заказать">
                        <h4 class="h4" id="oc-error"></h4>
                        <span id="oc-request-wait">{l s='Ожидание запроса...' mod='moonfriend'}</span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="oc-notification">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="h4" style="text-align: center">{l s='Заказ в один клик' mod='moonfriend'}</h4>
        </div>
        <div class="modal-body">
            <h4 class="h4" style="text-align: center">{$MSG}</h4>
        </div>
        <div class="modal-footer">
            <button id="oc-close-msg" type="button" class="btn-primary">{l s='Закрыть' mod='moonfriend'}</button>
        </div>
    </div>
</div>

