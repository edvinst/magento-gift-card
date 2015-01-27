var GiftCardTemplate = {
    config: {},

    init: function(config)
    {
        GiftCardTemplate.config = config;
        GiftCardTemplate.changeDesign($('design').value)
    },

    changeDesign: function(designName)
    {
        GiftCardTemplate.hideAll();
        if (designName == '')
            return;

        var designFieldSet = $('gift_card_design_'+designName);
        designFieldSet.show();
        designFieldSet.previous('.entry-edit-head').show();

        GiftCardTemplate.loadDesign(designName);
    },


    hideAll: function()
    {
        $$('.gift_card_design_fieldset').each(function(item){
            item.hide();
            item.previous('.entry-edit-head').hide();

        });


    },

    loadDesign: function(designName)
    {
        var explain = GiftCardTemplate.config.designPath+designName+'/thumb.jpg';

        //var thumbHtml = '<div class="gift_card_thumb"> <b>Gift Card Example</b><br/><img  src="'+explain+'"/></div>';
        var previewHtml = '<div class="gift_card_preview"> <b>Gift Card Preview</b><br/><img class="gift_card_preview_img" id="gift_card_preview_'+designName+'" src=""/></div>';
        if($('gift_card_design_content_'+designName))
            $('gift_card_design_content_'+designName).remove();
        $$('#gift_card_design_'+designName+' .hor-scroll').each(function(item){
            item.insert('<div id="gift_card_design_content_'+designName+'" class="gift_card_design_content">'+previewHtml+'</div>');
            GiftCardTemplate.loadPreview(designName);
        });




        //designFieldSet.down('.hor-scroll').each(function(item){
            //item.insert('<div>s</div>');
       // });
       // $$('#gift_card_design_'+designName+' .hor-scroll').insert('<div id="gift_card_design_'+designName+'_content" class="gift_card_design_content">mangoes</div>');
    },

    loadPreview: function(designName)
    {
        var previewLink = GiftCardTemplate.config.preview_link;
        $$('#gift_card_design_'+designName+' .gift_card_design_value').each(function(item){
            if (item.value != '')
                previewLink = previewLink+item.id+'/'+encodeURIComponent(item.value)+'/';
        });
        var idElement = $('template_id');
        var designElement = $('design');
        previewLink = previewLink+'template_id/'+encodeURIComponent(idElement.value)+'/';
        previewLink = previewLink+'design/'+encodeURIComponent(designElement.value)+'/';
        $('gift_card_preview_'+designName).src = previewLink;
    }
};
/*
document.observe("dom:loaded", function() {

});*/

