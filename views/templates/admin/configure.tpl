<form method="post">
    <div class="panel">
        <div class="panel-heading">
            {l s='Configuration' mod='moonfriend'}
        </div>
        <div class="panel-body">
            <label for="print">{l s='Сообщение при отправки формы' mod='moonfriend'}</label>
            <input type="text" name="print" id="print" class="form-control" value="{$MOONFRIEND_MSG}">
        </div>
        <div class="panel-footer">
            <button type="submit" name="save-msg" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='moonfriend'}
            </button>
        </div>
    </div>
</form>