jQuery(function ($) {
    let isRunning = false;
    let menu_admin_form = $('.wpss-menage-admin-menus');
    let admin_alerts = $('.role-editor-messages');
    let check_all = $('#select-all');
    let document = $('body');

    /** Menu Pages Action */
    $('#wpss-roles-list').select2();
    menu_admin_form.on('submit', function (e) {
        e.preventDefault();
        if (!isRunning) {
            isRunning = true;
            $.ajax({
                url: wpss_user_management_object.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    action: 'menage_admin_menu_options_action',
                    nonce: wpss_user_management_object.nonce,
                    wpss_admin_menus: $(this).serialize()
                },
                beforeSend: function () {
                    document.addClass('wpss-role-editor-loading');
                }
            }).success(function (data) {
                admin_alerts.removeClass('d-none').addClass('success').html(data);
            }).complete(function () {
                isRunning = false;
                document.removeClass('wpss-role-editor-loading');
            }).error(function (data) {
                admin_alerts.removeClass('d-none').addClass('error').html(data.status, data.statusText);
            });
        }
    });
    admin_alerts.on('click', function () {
        $(this).addClass('d-none');
    });
    check_all.on('click', function () {
        if ($(this).is(':checked')) {
            $('.pages-list').find('input:checkbox').prop('checked', true)
        } else {
            $('.pages-list').find('input:checkbox').prop('checked', false)
        }
    });

    /** Manage Roles Action */
    let form_roles = $('.form-roles');
    let role_delete = $('.role-delete');
    let role_delete_confirm = $('.role-delete-confirm-msg');
    let form_container = $('.table-container');

    /** Delete Roles Action */
    let role_name = false;
    let role_id = false;
    let parent_remove = false;
    role_delete.each(function () {
        $(this).on('click', 'span', function () {
            role_name = $(this).attr('data-role-name');
            role_id = $(this).attr('data-role-id');
            parent_remove = $('#role-' + role_id);
            role_delete_confirm.addClass('show-box');
            role_delete_confirm.find('.role-name > strong').text(role_name);
            $('.table-roles').addClass('move-left');

            if (form_container.outerHeight() < role_delete_confirm.outerHeight()) {
                form_container.animate({'height': role_delete_confirm.outerHeight()}, 200);
            }
        });
    });

    /** Confirm remove role action */
    role_delete_confirm.on('click', '.confirm-delete', function () {
        if (!isRunning) {
            isRunning = true;
            $.ajax({
                url: wpss_user_management_object.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    action: 'wpss_remove_role_action',
                    nonce: wpss_user_management_object.nonce,
                    role_id: role_id,
                    role_name: role_name,
                },
                beforeSend: function () {
                    document.addClass('wpss-role-editor-loading');
                }
            }).success(function (data) {
                admin_alerts.removeClass('d-none').addClass('success').html(data);
            }).complete(function () {
                isRunning = false;
                document.removeClass('wpss-role-editor-loading');
                parent_remove.remove();
                role_delete_confirm.removeClass('show-box');
                $('.table-roles').removeClass('move-left');
                form_container.removeAttr('style');
            }).error(function (data) {
                admin_alerts.removeClass('d-none').addClass('error').html(data.status, data.statusText);
            });
        }
    });

    /** Cancel remove role action */
    role_delete_confirm.on('click', '.cancel-delete', function () {
        role_delete_confirm.removeClass('show-box');
        $('.table-roles').removeClass('move-left');
    });

    /** Insert Roles Action */
    form_roles.on('submit', function (e) {
        e.preventDefault();
        if (!isRunning) {
            isRunning = true;
            $.ajax({
                url: wpss_user_management_object.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    action: 'wpss_add_roles_action',
                    nonce: wpss_user_management_object.nonce,
                    role: $(this).serialize()
                },
                beforeSend: function () {
                    document.addClass('wpss-role-editor-loading');
                }
            }).success(function (data) {
                admin_alerts.removeClass('d-none').addClass('success').html(data);
            }).complete(function () {
                isRunning = false;
                document.removeClass('wpss-role-editor-loading');
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }).error(function (data) {
                admin_alerts.removeClass('d-none').addClass('error').html(data.status, data.statusText);
            });
        }
    });

    /** User management events */
    $('#wpss-user-select').select2();
    let user_id = null;
    let user_details_container = $('.user-details-container');

    document.on('click', '.wpss-user-edit-link', function () {
        user_id = $(this).attr('data-user-id');
        if (user_id !== null && user_id !== '') {
            if (!isRunning) {
                isRunning = true;
                $.ajax({
                    url: wpss_user_management_object.ajax_url,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'wpss_get_user_details_action',
                        nonce: wpss_user_management_object.nonce,
                        user_id: user_id
                    },
                    beforeSend: function () {
                        document.addClass('wpss-role-editor-loading');
                    }
                }).success(function (data) {
                    user_details_container.removeClass('d-none').find('div').html(data);
                }).complete(function () {
                    isRunning = false;
                    document.removeClass('wpss-role-editor-loading');
                    $('html, body').animate({
                        scrollTop: document.find('#user-details-container').offset().top
                    }, 1000);
                }).error(function (data) {
                    user_details_container.removeClass('d-none').addClass('error').html(data.status, data.statusText);
                });
            }
        }
    });

    /** Get user details */
    $('.wpss-user-select').on('change', '#wpss-user-select', function () {
        user_id = $(this).val();
        if (user_id !== null && user_id !== '') {
            if (!isRunning) {
                isRunning = true;
                $.ajax({
                    url: wpss_user_management_object.ajax_url,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'wpss_get_user_details_action',
                        nonce: wpss_user_management_object.nonce,
                        user_id: user_id
                    },
                    beforeSend: function () {
                        document.addClass('wpss-role-editor-loading');
                    }
                }).success(function (data) {
                    user_details_container.removeClass('d-none').find('div').html(data);
                }).complete(function () {
                    isRunning = false;
                    document.removeClass('wpss-role-editor-loading');
                }).error(function (data) {
                    user_details_container.removeClass('d-none').addClass('error').html(data.status, data.statusText);
                });
            }
        }
    });

    /** Set/Remove user role */
    document.on('submit', '.wpss-add-role-to-user', function (e) {
        e.preventDefault();
        if (user_id !== null && user_id !== '') {
            if (!isRunning) {
                isRunning = true;
                $.ajax({
                    url: wpss_user_management_object.ajax_url,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'wpss_set_user_roles_action',
                        nonce: wpss_user_management_object.nonce,
                        user_id: user_id,
                        user_roles: $(this).serialize()
                    },
                    beforeSend: function () {
                        document.addClass('wpss-role-editor-loading');
                    }
                }).success(function (data) {
                    if (data !== '') {
                        admin_alerts.removeClass('d-none').addClass('success').html(data);
                    } else {
                        admin_alerts.removeClass('d-none').addClass('success').html("No changes made");
                    }
                }).complete(function () {
                    isRunning = false;
                    document.removeClass('wpss-role-editor-loading');
                    $('.wpss-add-role-to-user input[type="checkbox"]').each(function () {
                        if ($(this).is(':checked')) {
                            let input_val = $(this).val();
                            let input_text = $(this).parent().text();
                            $('.table-user-roles tbody tr').each(function () {
                                if (!$('.user-role-' + input_val)[0]) {
                                    $(this).parent().append('<tr class="user-role-' + input_val + '"><td>' + input_text + '</td></tr>')
                                }
                            });
                        } else {
                            $('.table-user-roles tbody').find('.user-role-' + $(this).val()).remove();
                        }
                    });
                }).error(function (data) {
                    user_details_container.removeClass('d-none').addClass('error').html(data.status, data.statusText);
                });
            }
        }
    });

    /** Capabilities management events */
    $('#wpss-role-select').select2();
    let role_caps = null;
    let role_caps_container = $('.wpss-role-caps-container');

    /** Get role capabilities action */
    $('.wpss-role-select').on('change', '#wpss-role-select', function () {
        role_caps = $(this).val();
        if (role_caps !== null && role_caps !== '') {
            if (!isRunning) {
                isRunning = true;
                $.ajax({
                    url: wpss_user_management_object.ajax_url,
                    type: 'POST',
                    cache: false,
                    data: {
                        action: 'wpss_get_role_capabilities_action',
                        nonce: wpss_user_management_object.nonce,
                        role_caps: role_caps,
                    },
                    beforeSend: function () {
                        document.addClass('wpss-role-editor-loading');
                    }
                }).success(function (data) {
                    role_caps_container.removeClass('d-none').find('div').html(data);
                }).complete(function () {
                    isRunning = false;
                    document.removeClass('wpss-role-editor-loading');
                    /* Check/Uncheck capabilities groups */
                    $('.caps-container').each(function () {
                        $(this).on('click', 'strong input[type="checkbox"]', function () {
                            if ($(this).is(':checked')) {
                                $(this).closest('ul').find('input[type="checkbox"]').prop('checked', true);
                            } else {
                                $(this).closest('ul').find('input[type="checkbox"]').prop('checked', false);
                            }
                        });
                    });
                }).error(function (data) {
                    role_caps_container.removeClass('d-none').addClass('error').html(data.status, data.statusText);
                });
            }
        }
    });

    /** Set capabilities to role action */
    document.on('submit', '.wpss-add-caps-to-role', function (e) {
        e.preventDefault();
        if (!isRunning) {
            isRunning = true;
            $.ajax({
                url: wpss_user_management_object.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    action: 'wpss_set_capabilities_to_role_action',
                    nonce: wpss_user_management_object.nonce,
                    role: role_caps,
                    capabilities: $(this).serialize(),
                },
                beforeSend: function () {
                    document.addClass('wpss-role-editor-loading');
                }
            }).success(function (data) {
                if (data !== '') {
                    admin_alerts.removeClass('d-none').addClass('success').html(data);
                } else {
                    admin_alerts.removeClass('d-none').addClass('success').html("No changes made");
                }
            }).complete(function () {
                isRunning = false;
                document.removeClass('wpss-role-editor-loading');
            }).error(function (data) {
                admin_alerts.removeClass('d-none').addClass('error').html(data.status, data.statusText);
            });
        }
    });

    /** Caps live search */
    document.on('keyup', '.cap-filter', function () {
        let filter = $(this).val();
        let count = 0;
        $(this).next('div').find('li').each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
                $(this).parent()
                    .css('border-bottom', 'none')
                    .css('margin', 0)
                    .css('padding', 0)
                    .css('flex', 'inherit')
                ;
            } else {
                $(this).show();
                $(this).parent().removeAttr('style');
                count++;
            }
        });
    });
});
