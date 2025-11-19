/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var faq_func = {
    search: function () {
        var faq_input = $('.faq_group_form .faq_form .auto_search_complete');
        var faq_product_ids = $('.faq_group_form .faq_form input[name=product_ids]');
        if (faq_input.length > 0 && faqBaseAdminUrl != '' && faq_product_ids.length > 0) {
            $(faq_input).autocomplete(faqBaseAdminUrl, {
                minChars: 1,
                autoFill: true,
                max: 20,
                matchContains: true,
                mustMatch: true,
                scroll: true,
                cacheLength: 0,
                formatItem: function (item) {
                    return '<img src="' + item[2] + '" alt="' + item[0] + '"/>' + item[1] + ' - ' + item[0] + (item[3] ? ' (Ref:' + item[3] + ')' : '');
                }
            }).result(function (event, data, formatted) {
                if (data == null)
                    return false;
                if (!faq_product_ids.val()) {
                    faq_product_ids.val(data[1]);
                    faq_input.parent('.faq_search_product')
                        .next('ul.faq-product-list')
                        .append('<li><img src="' + data[2] + '" alt="' + data[0] + '" /><span class="product-name">' + data[0] + '</span>' + (data[3] ? ' (Ref:' + data[3] + ')' : '') + ' <span class="delete-product-id" data-id="' + data[1] + '"><i class="icon-remove"></i></span></li>');
                } else {
                    var productIds = faq_product_ids.val().split(",");
                    if (productIds.indexOf(data[1]) == -1) {
                        faq_product_ids.val(faq_product_ids.val() + ',' + data[1]);
                        faq_input.parent('.faq_search_product')
                            .next('ul.faq-product-list')
                            .append('<li><img src="' + data[2] + '" alt="' + data[0] + '" /><span class="product-name">' + data[0] + '</span>' + (data[3] ? ' (Ref:' + data[3] + ')' : '') + ' <span class="delete-product-id" data-id="' + data[1] + '"><i class="icon-remove"></i></span></li>');
                    }
                }
                faq_input.val('');
            });
        }
    },
    remove: function (parent, element) {
        var ax = -1;
        if ((ax = parent.indexOf(element)) !== -1)
            parent.splice(ax, 1);
        return parent;
    },
    display_on_product: function () {
        if ($('#display_on_product').length > 0) {
            if (parseInt($('#display_on_product').val()) != 3) {
                $('.row_question_product').slideUp(300);
            } else {
                $('.row_question_product').slideDown(300).removeClass('hidden');
            }
        }
    },
    get_alias: function (faq_is_updating, from_alias, to_alias, preview_link) {
        if (!faq_is_updating) {
            to_alias.val(str2url(from_alias.val(), 'UTF-8'));
        }
        else {
            if (to_alias.val() == '')
                to_alias.val(str2url(from_alias.val(), 'UTF-8'));
        }
        if (to_alias.val() != '' && preview_link) {
            var _href = to_alias.parent().children('span').children('a.preview_link').attr('ref') + to_alias.val();
            to_alias.parent().children('span').children('a.preview_link').attr('href', _href);
            to_alias.parent().children('span').children('a.preview_link').html(_href);
        }
    },

}

/*delete product from list product*/
$(document).on('click', '.delete-product-id', function (e) {
    e.preventDefault();
    var faq_product_id = $(this).attr('data-id');
    var faq_product_ids = $('input[name=product_ids]').val().split(",");
    if (faq_product_ids.indexOf(faq_product_id) != -1) {
        $('input[name=product_ids]').val(faq_func.remove(faq_product_ids, faq_product_id));
        $(this).closest('li').remove();
    }
});

$(document).ready(function () {
    //First Open
    if ($('.faq_parent_ul > li').length > 0) {
        $('.faq_parent_ul > li:first-child').addClass('open');
    }
    // Sort
    faqSort('.faq_group_tabs');
    faqSort('.faq_question_ul');
});

$(document).on('change', 'input[name*=ETS_FAQ_META_TITLE_]', function (e) {
    e.preventDefault();
    if (PS_REWRITING_SETTINGS){
        var obj_alias = $('input[name=ETS_FAQ_REWRITE_URL_' + $(this).attr('id').replace('ETS_FAQ_META_TITLE_', '') + ']');
        faq_func.get_alias(false, $(this), obj_alias, true);
    }
});

$(document).on('change', 'input[name*=ETS_FAQ_REWRITE_URL_]', function (e) {
    e.preventDefault();
    if (PS_REWRITING_SETTINGS) {
        faq_func.get_alias(false, $(this), $(this), true);
    }
});

$(document).on('change', 'input[name=ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT]', function (e) {
    e.preventDefault();
    if (parseInt($(this).val()) > 0) {
        $('.row_ets_faq_tab_title_on_product_page').slideDown(300);
        $('.row_ets_faq_position_on_product_page').slideDown(300);
    }
    else {
        $('.row_ets_faq_tab_title_on_product_page').slideUp(300);
        $('.row_ets_faq_position_on_product_page').slideUp(300);
    }
});

$(document).on('click', '.faq_group_delete', function () {
    if (!confirm(faq_msg_delete))
        return false;
    if (!$(this).hasClass('active') && parseInt($(this).parents('li').eq(0).data('id-faq_group')) > 1) {
        $(this).addClass('active');
        $.ajax({
            url: faqBaseAdminUrl,
            dataType: 'json',
            type: 'post',
            data: {
                itemId: $(this).parents('li').eq(0).data('id-faq_group'),
                deleteobject: 1,
                faq_object: 'FAQ_Group',
            },
            success: function (json) {
                if (json.success) {
                    if ($('.faq_group_li.item' + json.itemId).prev('li').length > 0)
                        $('.faq_group_li.item' + json.itemId).prev('li').addClass('open');
                    else if ($('.faq_group_li.item' + json.itemId).next('li').length > 0)
                        $('.faq_group_li.item' + json.itemId).next('li').addClass('open');
                    $('.faq_group_li.item' + json.itemId).remove();
                    faqAlertSucccess(json.successMsg);
                }
                else
                    $('.faq_group_li.item' + json.itemId + ' .faq_group_delete').removeClass('active');
            },
            error: function (xhr, status, error) {
                $('.faq_group_delete').removeClass('active');
            }
        });
    }
    return false;
});

$(document).on('click', '.faq_question_delete', function () {
    if (!confirm(faq_msg_delete))
        return false;
    if (!$(this).hasClass('active')) {
        var id_faq_group = $(this).parents('li').eq(0).data('id-faq_group');
        $(this).addClass('active');
        $.ajax({
            url: faqBaseAdminUrl,
            dataType: 'json',
            type: 'post',
            data: {
                itemId: $(this).parents('li').eq(0).data('id-faq_question'),
                deleteobject: 1,
                faq_object: 'FAQ_Question',
            },
            success: function (json) {
                if (json.success) {
                    $('.faq_question_li.item' + json.itemId).remove();
                    if ($('.faq_question_li[data-id-faq_group = ' + id_faq_group + ']').length <= 0)
                        $('.faq_no_questions.group' + id_faq_group).removeClass('hidden');
                    faqAlertSucccess(json.successMsg);
                }
                else
                    $('.faq_question_li.item' + json.itemId + ' .faq_question_delete').removeClass('active');
            },
            error: function (xhr, status, error) {
                $('.faq_question_delete').removeClass('active');
            }
        });
    }
    return false;
});

$(document).on('click', '.faq_group_edit', function () {
    if (!$(this).hasClass('active')) {
        $('.ets_faq').addClass('loading');
        $(this).addClass('active');
        $('.faq-alert').remove();
        $.ajax({
            url: faqBaseAdminUrl,
            dataType: 'json',
            type: 'post',
            data: {
                itemId: $(this).parents('li').eq(0).data('id-faq_group'),
                request_form: 1,
                faq_object: 'FAQ_Group',
            },
            success: function (json) {
                $('.ets_faq').removeClass('loading');
                showSaveMessage(json.alert);
                $('.faq_pop_up').addClass('hidden');
                $('.faq_forms').removeClass('hidden');
                $('.faq_group_form').removeClass('hidden');
                $('.faq_group_form .faq_form').html(json.form);
                $('.faq_group_li.item' + json.itemId + ' .faq_group_edit').removeClass('active');
                $('.faq_group_li').removeClass('open');
                $('.faq_group_li.item' + json.itemId).addClass('open');
            },
            error: function (xhr, status, error) {
                $('.ets_faq').removeClass('loading');
                $('.faq_group_edit').removeClass('active');
            }
        });
    }
});

$(document).on('click', '.faq_question_edit', function () {
    if (!$(this).hasClass('active')) {
        $('.ets_faq').addClass('loading');
        $(this).addClass('active');
        $('.faq-alert').remove();
        $.ajax({
            url: faqBaseAdminUrl,
            dataType: 'json',
            type: 'post',
            data: {
                itemId: $(this).parents('li').eq(0).data('id-faq_question'),
                request_form: 1,
                faq_object: 'FAQ_Question',
            },
            success: function (json) {
                $('.faq_pop_up').addClass('hidden');
                $('.faq_forms').removeClass('hidden');
                $('.faq_group_form').removeClass('hidden');
                $('.faq_group_form .faq_form').html(json.form);
                $('.faq_question_li.item' + json.itemId + ' .faq_question_edit').removeClass('active');
                faq_func.search();
                faq_func.display_on_product();
                $('.ets_faq').removeClass('loading');
                // if($('.autoload_rte').length)
                // {
                //     var date_time = new Date();
                //     $('.autoload_rte').each(function(){
                //         $(this).attr('id',$(this).attr('id')+date_time.getTime());
                //     });
                //     tinySetup({
            	// 		editor_selector :"autoload_rte"
            	// 	});
                // }
            },
            error: function (xhr, status, error) {
                $('.ets_faq').removeClass('loading');
                $('.faq_question_edit').removeClass('active');
            }
        });
    }
});

$(document).on('change', '#display_on_product', function (e) {
    e.preventDefault();
    if (parseInt($(this).val()) != 3) {
        $(this).parents('.form-wrapper').children('.row_question_product').slideUp(300);
    } else {
        $(this).parents('.form-wrapper').children('.row_question_product').slideDown(300).removeClass('hidden');
    }
});


$(document).keyup(function (e) {
    if (e.keyCode === 27) {
        $('.faq_pop_up').addClass('hidden');
        $('.faq_forms').addClass('hidden');
        $('.faq_export_form').addClass('hidden');
        $('.faq_overlay').addClass('hidden');
    }
});

$(document).on('click', '.faq_overlay', function (event) {
    event.preventDefault();
    $('.faq_pop_up').addClass('hidden');
    $('.faq_forms').addClass('hidden');
    $('.faq_overlay').addClass('hidden');
});

$(document).on('click', '.faq_pop_up', function (event) {
    event.stopPropagation();
});

$(document).on('click', '.faq_close', function () {

    $(this).parent('.faq_pop_up').addClass('hidden');
    $(this).parent().parent('.faq_pop_up').addClass('hidden');
    $(this).parent().parent('.faq_forms').addClass('hidden');
    $('.faq_overlay').addClass('hidden');
});

$(document).on('click', '.faq_group_tabs .faq_group_li', function (e) {
    e.preventDefault();
    if (!$(this).hasClass('open')) {
        $('.faq_group_li').removeClass('open');
        $('.faq_group_li.item' + $(this).data('id-faq_group')).addClass('open');
    }
});

$(document).on('click', '.question_name', function (e) {
    e.preventDefault();
    var id_faq_group = $(this).parents('li').data('id-faq_group');
    if (!$(this).next('.answer').hasClass('open') && !$(this).hasClass('open')) {
        $('.faq_question_li[data-id-faq_group = ' + id_faq_group + '] .question_name').removeClass('open');
        $('.faq_question_li[data-id-faq_group = ' + id_faq_group + '] .question_name').next('.answer').removeClass('open');
        $(this).addClass('open');
        $(this).next('.answer').addClass('open');
    }
    else {
        $(this).removeClass('open');
        $(this).next('.answer').removeClass('open');
    }
});

$(document).on('click', '.faq_add_group', function () {
    $('.faq_pop_up').addClass('hidden');
    $('.faq_group_form').removeClass('hidden');
    $('.faq_forms').removeClass('hidden');
    if ($('.faq_group_form .faq_form form input[name="itemId"]').length <= 0 || $('.faq_group_form .faq_form form input[name="faq_object"]') != 'FAQ_Group' || $('.faq_group_form .faq_form form input[name="itemId"]').length > 0 && parseInt($('.faq_group_form .faq_form form input[name="itemId"]').val()) != 0)
        $('.faq_group_form .faq_form').html($('.faq_group_form_new').html());
    $('.faq-alert').remove();
    return false;
});

$(document).on('click', '.faq_group_add_question', function () {
    var obj = $(this);
    if (!$(this).hasClass('active')) {
        $('.ets_faq').addClass('loading');
        $(this).addClass('active');
        $('.faq-alert').remove();
        $.ajax({
            url: faqBaseAdminUrl,
            dataType: 'json',
            type: 'post',
            data: {
                request_form: 1,
                faq_object: 'FAQ_Question',
            },
            success: function (json) {
                $('.ets_faq').removeClass('loading');
                $('.faq_pop_up').addClass('hidden');
                $('.faq_forms').removeClass('hidden');
                $('.faq_group_form').removeClass('hidden');
                if ($('.faq_group_form .faq_form form input[name="itemId"]').length <= 0 || $('.faq_group_form .faq_form form input[name="faq_object"]') != 'FAQ_Question' || $('.faq_group_form .faq_form form input[name="itemId"]').length > 0 && (parseInt($('.faq_group_form .faq_form form input[name="itemId"]').val()) != 0 || parseInt($('.faq_group_form .faq_form form input[name="itemId"]').val()) == 0 && parseInt($('.faq_group_form .faq_form form input[name="id_faq_group"]').val())) != parseInt(obj.parents('li').attr('data-id-faq_group'))) {
                    $('.faq_group_form .faq_form').html(json.form);
                    $('.faq_group_form .faq_form form input[name="id_faq_group"]').val(obj.parents('li').attr('data-id-faq_group'));
                    // if($('.autoload_rte').length)
                    // {
                    //     var date_time = new Date();
                    //     $('.autoload_rte').each(function(){
                    //         $(this).attr('id',$(this).attr('id')+date_time.getTime());
                    //     });
                    //     tinySetup({
                	// 		editor_selector :"autoload_rte"
                	// 	});
                    // }
                }
                obj.removeClass('active');
                faq_func.search();
                faq_func.display_on_product();
            },
            error: function (xhr, status, error) {
                $('.faq_group_add_question').removeClass('active');
                $('.ets_faq').removeClass('loading');
            }
        });
    }
    return false;
});

$(document).on('click', '.faq_config_button', function () {
    if ($('input:radio[id=ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT_on]').is(':checked')) {
        $('.row_ets_faq_tab_title_on_product_page').slideDown(300);
        $('.row_ets_faq_position_on_product_page').slideDown(300);
    }
    else {
        $('.row_ets_faq_tab_title_on_product_page').slideUp(300);
        $('.row_ets_faq_position_on_product_page').slideUp(300);
    }
    $('.faq_pop_up').addClass('hidden');
    $('.faq_overlay').addClass('hidden');
    $('.faq_config_form').removeClass('hidden');
    $('.faq_config_form').parents('.faq_overlay').removeClass('hidden');
    $('.faq-alert.alert-success').remove();
});

$(document).on('click', '.faq_save', function (e) {
    e.preventDefault();
    if (!$(this).parents('form').eq(0).hasClass('active') && $('.defaultForm.active').length <= 0) {
        if (typeof tinymce !== 'undefined' && tinymce.editors.length > 0) {
            tinyMCE.triggerSave();
        }
        $(this).parents('form').eq(0).addClass('active');
        $(this).parents('.faq_save_wrapper').eq(0).addClass('faq_saving_enabled');
        $('.faq-alert').remove();
        var formData = new FormData($(this).parents('form').get(0));
        var id_faq_group = $(this).parents('form').eq(0).find('input#id_faq_group').val();
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (json) {
                showSaveMessage(json.alert);
                if (json.images && json.success) {
                    $.each(json.images, function (i, item) {
                        if ($('.defaultForm.active input[name="' + item.name + '"]').length > 0) {
                            updatePreviewImage(item.name, item.url, item.delete_url);
                        }
                    });
                }
                if (json.itemId && json.itemKey && json.success) {
                    $('.defaultForm.active input[name="' + json.itemKey + '"]').val(json.itemId);
                    $('.defaultForm.active input[name="itemId"]').val(json.itemId);
                }
                if (json.success) {
                    if (json.faq_object == 'FAQ_Group') {
                        /*list*/
                        if ($('.faq_group_ul .faq_group_li.item' + json.itemId).length > 0) {
                            $('.faq_group_ul .faq_group_li.item' + json.itemId).replaceWith(json.grouplist);
                        }
                        else {
                            $('.faq_group_list .faq_group_ul').append(json.grouplist);
                        }
                        /*tab*/
                        if ($('.faq_group_tab .faq_group_li.item' + json.itemId).length > 0) {
                            $('.faq_group_tab .faq_group_li.item' + json.itemId).replaceWith(json.grouptab);
                        } else {
                            $('.faq_group_tab .faq_group_tabs').append(json.grouptab);
                        }

                        if ($('.faq_group_li.item' + json.itemId).length > 0) {
                            $('.faq_group_li').removeClass('open');
                            $('.faq_group_li.item' + json.itemId).addClass('open');
                        }
                    }

                    if (json.faq_object == 'FAQ_Question' && json.success) {
                        if ($('.faq_question_li.item' + json.itemId).length > 0) {
                            if (json.questionHtml)
                                $('.faq_question_li.item' + json.itemId).replaceWith(json.questionHtml);
                            else
                                $('.faq_question_li.item' + json.itemId).remove();
                        } else {
                            if ($('.faq_group_ul .faq_group_li.open .faq_question_ul').length > 0) {
                                $('.faq_group_ul .faq_group_li.open .faq_question_ul').append(json.questionHtml);
                            } else {
                                $('.faq_group_li.open .faq_question_wrapper').append(json.questionHtml);
                            }
                        }

                        if ($('.faq_question_li[data-id-faq_group = ' + id_faq_group + ']').length > 0) {
                            $('.faq_no_questions.group' + id_faq_group).addClass('hidden');
                        }

                        if ($('.faq_question_li[data-id-faq_group = ' + id_faq_group + '].item' + json.itemId + ' .answer').length > 0) {
                            $('.faq_question_li[data-id-faq_group = ' + id_faq_group + '] .answer').removeClass('open');
                            $('.faq_question_li[data-id-faq_group = ' + id_faq_group + '].item' + json.itemId + ' .answer').addClass('open');
                        }

                    }
                    if ($('.faq-alert-' + json.time).length > 0) {
                        faqAlertSucccess($('.faq-alert-' + json.time + '.alert-success').html());
                    }
                    $('.faq_pop_up').addClass('hidden').parents('.faq_forms').addClass('hidden');
                    $('.faq_overlay').addClass('hidden');
                }
                $('.defaultForm.active').removeClass('active');
                $('.faq_save_wrapper').removeClass('faq_saving_enabled');


            },
            error: function (xhr, status, error) {
                $('.defaultForm.active').removeClass('active');
                $('.faq_save_wrapper').removeClass('faq_saving_enabled');
            }

        });

    }
    return false;
});

$(document).on('click', '.faq_config_save', function () {
    if (!$('.faq_config_form_content').hasClass('active')) {
        if ($('input.tagify').length > 0) {
            $('input.tagify').each(function () {
                $(this).val($(this).tagify('serialize'));
            });
        }
        $('.faq_config_form_content').addClass('active');
        $(this).parents('.faq_save_wrapper').eq(0).addClass('faq_saving_enabled');
        $('.faq-alert').remove();
        var formData = new FormData($(this).parents('form').get(0));
        $.ajax({
            url: $(this).parents('form').eq(0).attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (json) {
                $('.faq-alert').remove();
                $('.faq_config_form_content').removeClass('active');
                $('.faq_config_form_content').append(json.alert);
                if (json.success) {
                    faqAlertSucccess($('.faq_config_form_content .faq-alert').html());
                    $('.faq_config_form.faq_pop_up').addClass('hidden');
                    $('.faq_overlay').addClass('hidden');
                }
                $('.faq_save_wrapper').removeClass('faq_saving_enabled');
            },
            error: function (xhr, status, error) {
                $('.faq-alert').remove();
                $('.faq_save_wrapper').removeClass('faq_saving_enabled');
                $('.faq_config_form_content').removeClass('active');
            }
        });
    }
    return false;
});

function faqAlertSucccess(successMsg) {
    if ($('#content .ets_faq_success_alert').length <= 0) {
        $('#content').append('<div class="alert alert-success ets_faq_success_alert" style="display: none;"></div>');
    }
    $('#content .ets_faq_success_alert').html(successMsg);
    $('#content .ets_faq_success_alert').fadeIn().delay(5000).fadeOut();
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if ($(input).parents('.col-lg-9').eq(0).find('.preview_img').length <= 0) {
                $(input).parents('.col-lg-9').eq(0).append('<div class="preview_img"><img src="' + e.target.result + '"/> <i style="font-size: 20px;" class="process-icon-delete del_preview" data-title="&#xE872;"></i></div>');
            }
            else {
                $(input).parents('.col-lg-9').eq(0).find('.preview_img img').eq(0).attr('src', e.target.result);
            }
            if ($(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').length > 0) {
                $(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').addClass('hidden');
                $(input).parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.uploaded_img_wrapper').addClass('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function updatePreviewImage(name, url, delete_url) {
    if ($('.defaultForm.active input[name="' + name + '"]').length > 0 && $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').length > 0) {
        if ($('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).find('.preview_img').length > 0)
            $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).find('.preview_img').eq(0).remove();
        if ($('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).next('.uploaded_image_label').length <= 0) {
            $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).after('<label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">Uploaded image: </label><div class="col-lg-9 uploaded_img_wrapper"><img title="Click to see full size image" style="display: inline-block; max-width: 200px;" src="' + url + '">' + (delete_url ? '<a class="delete_url" style="display: inline-block; text-decoration: none!important;" href="' + delete_url + '"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>' : '') + '</div>');
        }
        else {
            var imageWrapper = $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.col-lg-9');
            if (imageWrapper.find('a.delete_url').length > 0)
                imageWrapper.find('a.delete_url').eq(0).attr('href', delete_url);
            $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).next('.uploaded_image_label').removeClass('hidden');
            $('.defaultForm.active input[name="' + name + '"]').parents('.col-lg-9').eq(0).next('.uploaded_image_label').next('.uploaded_img_wrapper').removeClass('hidden');
        }
        $('.defaultForm.active input[name="' + name + '"]').val('');
    }
}

function showSaveMessage(alertmsg) {
    if (alertmsg) {
        if ($('.defaultForm.active').parents('.faq_pop_up').eq(0).find('.alert').length > 0)
            $('.defaultForm.active').parents('.faq_pop_up').eq(0).find('.alert').remove();
        $('.defaultForm.active').parents('.faq_pop_up').eq(0).append(alertmsg);
    }
}

function faqSort(selector) {
    $(selector).sortable({
        update: function (e, ui) {
            if (this === ui.item.parent()[0]) {
                var obj = ui.item.attr('data-obj');
                var itemId = ui.item.attr('data-id-faq_' + obj);
                var previousId = ui.item.prev('li').length > 0 ? ui.item.prev('li').attr('data-id-faq_' + obj) : 0;
                $.ajax({
                    url: faqBaseAdminUrl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        itemId: itemId,
                        obj: obj,
                        previousId: previousId,
                        updateOrder: 1,
                    },
                    success: function (json) {
                        if (!json.success)
                            $(selector).sortable('cancel');
                        else {
                            faqAlertSucccess(json.success);
                        }
                    },
                    error: function () {
                        $(selector).sortable('cancel');
                    }
                });
            }
        }
    }).disableSelection();
}