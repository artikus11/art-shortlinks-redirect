jQuery(function ($) {
    $('.redirect_link').on('blur', function () {
        var redirect_link = $(this);
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'update_redirect_url',
                nonce: asr_ajax.nonce,
                redirect_val: redirect_link.val(),
                redirect_id: redirect_link.attr('data-redirect-id'),
            },
            dataType: 'json',
            beforeSend: function (xhr) {
                redirect_link.attr('readonly', 'readonly').next().addClass('is-active');
            },
            success: function (results) {
                redirect_link.removeAttr('readonly').next().removeClass('is-active');
            }
        });
    });
});
