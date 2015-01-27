
var GiftCard = {

    importRequiredAdditionalFields: {},

    changeImportAction: function(value)
    {
        if (value == 'all')
            GiftCard.hideImportAdditionalFields();
        else
            GiftCard.showImportAdditionalFields();
    },

    hideImportAdditionalFields: function()
    {
        var i = 0;
        $$('.giftcard-import-additional').each(function(element) {
            if (element.hasClassName('required-entry')) {
                GiftCard.importRequiredAdditionalFields[element.id] = 1;
                i++;
                element.removeClassName('required-entry').addClassName('ignore-validate');
            }
            element.up('tr').hide();
        });
    },

    showImportAdditionalFields: function()
    {
        var elmCount = GiftCard.importRequiredAdditionalFields.length;
        $$('.giftcard-import-additional').each(function(element) {
            if (GiftCard.importRequiredAdditionalFields[element.id]) {
                element.addClassName('required-entry').removeClassName('ignore-validate');
            }
            element.up('tr').show();
        });
    }
};


document.observe("dom:loaded", function() {
    Event.observe($('giftcard_import_action'),'change', function(){
        GiftCard.changeImportAction($('giftcard_import_action').value);
    });
});

