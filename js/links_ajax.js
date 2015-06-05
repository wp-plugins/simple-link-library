if( typeof(console) == 'object' ) {
    console.log( 'script loaded' );
}

function bms_yall2_ajaxload(catid,nonce) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxloadpostajax.ajaxurl,
        data: {
            action: 'bms_yall2_ajaxhandler',
            catid: catid,
            nonce: nonce
        },
        success: function(data, textStatus, XMLHttpRequest) {
            var loadpostresult = '#loadpostresult';
            jQuery(loadpostresult).html('');
            jQuery(loadpostresult).append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
