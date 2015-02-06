
var GiftCardOptions = {

    config: {},

    regularPriceSelector: '.product-options-bottom .regular-price .price, .product-shop .regular-price .price',
    specialPriceSelector: '.product-options-bottom .special-price .price, .product-shop .special-price .price',
    oldPriceSelector: '.product-options-bottom .old-price .price, .product-shop .old-price .price',

    init: function(config)
    {
        this.config = config;
        jQuery.each(this.config, function(i, item) {
            var elm = jQuery('#gift_card_'+config[i].id);
            jQuery(elm).change(function(){
                eval('GiftCardOptions.'+config[i].name+'Changed('+i+');');
            });
        });
    },

    valueChanged: function(elmId)
    {
        if (!GiftCardOptions.config[elmId])
            return;
        var optionConfig = GiftCardOptions.config[elmId];
        var elm = jQuery('#gift_card_'+optionConfig.id);
        if (elm.val() == '')
            return;

        var series = optionConfig.options[elm.val()];
        jQuery(GiftCardOptions.regularPriceSelector).each(function(){
            jQuery(this).text(series.formatedPrice);
        });

        jQuery(GiftCardOptions.specialPriceSelector).each(function(){
            var currentPrice = jQuery(this).text();
            jQuery(this).text(series.formatedPrice);
        });

        jQuery(GiftCardOptions.oldPriceSelector).each(function(){
            var currentPrice = jQuery(this).text();
            if (parseFloat(series.price) < parseFloat(series.oldPrice)) {
                jQuery(this).text(series.formatedOldPrice).show();
            } else {
                jQuery(this).hide();
            }
        });
    }
};


jQuery.noConflict();