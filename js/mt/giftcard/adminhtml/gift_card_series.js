var GiftCardSeries = {
    generate: function(formId, requestUrl, grid)
    {
        $(formId).removeClassName('ignore-validate');
        var validationResult = $(formId).select('input',
                'select', 'textarea').collect( function(elm) {
                return Validation.validate(elm, {
                    useTitle :false,
                    onElementValidate : function() {
                    }
                });
            }).all();
        $(formId).addClassName('ignore-validate');

        if (!validationResult) {
            return;
        }

        var params = {
            'status': $$('#'+formId+' #status')[0].value,
            'series_id': $$('#'+formId+' #series_id')[0].value,
            'qty': $$('#'+formId+' #qty')[0].value,
            'length': $$('#'+formId+' #default_length')[0].value,
            'format': $$('#'+formId+' #default_format')[0].value,
            'prefix': $$('#'+formId+' #default_prefix')[0].value,
            'suffix': $$('#'+formId+' #default_suffix')[0].value,
            'dash': $$('#'+formId+' #default_dash')[0].value
        };
        var giftcardgrid = eval(grid);
        new Ajax.Request(requestUrl, {
            parameters :params,
            method :'post',
            onComplete : function (transport, param){
                var response = false;
                if (transport && transport.responseText) {
                    response = eval('(' + transport.responseText + ')');
                }
                if (giftcardgrid)
                    giftcardgrid.reload();

                if (response && response.messages) {
                    $('messages').update(response.messages);
                }
                if (response && response.error) {
                    alert(response.error);
                }
            }
        });
    }
};
