'use strict';

jQuery(function ($) {
    /** @const wpss_user_management_object */
    let ajaxUrl = wpss_user_management_object.ajax_url;
    let nonce = wpss_user_management_object.nonce;
    let isRunning = false;
    let document = $('body');

    document.on('submit', '.wpss-settings-tab-form', function (e) {
        e.preventDefault();
        if (isRunning === false) {
            isRunning = true;
            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                data: {
                    action: 'saveSettings',
                    nonce: nonce,
                    settings: $(this).serialize(),
                },
                beforeSend: function () {
                    document.addClass('wpss-role-editor-loading');
                },
                success: function (response) {
                    document.find('.settings-message').removeClass('d-none');
                    document.on('click', '.settings-message', function () {
                        $(this).addClass('d-none');
                    });
                },
                complete: function () {
                    isRunning = false;
                    document.removeClass('wpss-role-editor-loading');
                }
            });
        }
    });
});