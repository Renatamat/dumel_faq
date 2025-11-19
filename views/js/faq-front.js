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
function ets_faq_resset_captcha() {
    if ($('.ets_faq_g_recaptcha').length) {
        grecaptcha.reset();
    } else if ($('#faq_captcha_refesh').length) {
        $('#faq_captcha_refesh').trigger('click');
    } else if ($('#ets_faq_g_recaptcha').length > 0) {
        ets_faq_g_recaptchaonloadCallback();
    }
}
$(document).ready(function () {
    if ($('.faq_group_ul > li').length > 0)
        $('.faq_group_ul > li:first-child').addClass('open');
    if ($('.faq_tab_content > .faq_tab_pane').length > 0)
        $('.faq_tab_content > .faq_tab_pane:first-child').addClass('open');
});

$(document).on('click', '.faq_nav_link', function (e) {
    e.preventDefault();
    $('.faq_group_ul > li').removeClass('open');
    $('.faq_tab_content > .faq_tab_pane').removeClass('open');
    $(this).parent('li').addClass('open');
    $($(this).attr('ruler')).addClass('open');
});

$(document).on('click', '#faq_captcha_refesh', function (e) {
    e.preventDefault();
    originalCapcha = $('.faq_captcha_image_data').attr('src');
    originalCode = $('#faq_captcha_refesh').attr('data-rand');
    newCode = Math.random();
    $('.faq_captcha_image_data').attr('src', originalCapcha.replace(originalCode, newCode));
    $('#faq_captcha_refesh').attr('data-rand', newCode);
});

$(document).keyup(function (e) {
    e.preventDefault();
    if (e.keyCode === 27) {
        $('.faq_pop_up').addClass('hidden');
        $('.faq_forms').addClass('hidden');
        $('.faq_ask_a_question_form').addClass('hidden');
        $('.faq_overlay').addClass('hidden');
    }
});

$(document).on('click', '.faq_pop_up', function (event) {
    event.stopPropagation();
});

$(document).on('click', '.faq_overlay', function (event) {
    event.preventDefault();
    $('.faq_pop_up').addClass('hidden');
    $('.faq_forms').addClass('hidden');
    $('.faq_overlay').addClass('hidden');
});

$(document).on('click', '.faq_close', function () {
    $(this).parent('.faq_pop_up').addClass('hidden');
    $(this).parent().parent('.faq_pop_up').addClass('hidden');
    $(this).parent().parent('.faq_forms').addClass('hidden');
    $('.faq_overlay').addClass('hidden');
});

$(document).on('click', '.faq_question_name', function (e) {
    e.preventDefault();
    if ($(this).next('.faq_answer').hasClass('open') && $(this).hasClass('open')) {
        $(this).removeClass('open');
        $(this).next('.faq_answer').removeClass('open');
    } else {
        $(this).addClass('open');
        $(this).next('.faq_answer').addClass('open');
    }
});

$(document).on('click', '#ask_a_question', function () {
    if ($('#your_question').length > 0)
        $('#your_question').val('');
    if ($('#faq_captcha_refesh').length)
        $('#faq_captcha_refesh').trigger('click');
    $('.faq_pop_up').addClass('hidden');
    $('.faq_ask_a_question_form').removeClass('hidden');
    $('.faq_forms').removeClass('hidden');
    $('.faq-alert').remove();
    return false;
});

$(document).on('click', '#faq_send_mail', function () {
    var obj = $(this);
    $('.faq_name-alert,.faq_name-valid,.faq_email-alert,.faq_email-valid,.faq_phone-alert,.faq_phone-valid,.faq_your_question-alert,.faq_your_question-valid,.captcha-alert,.captcha-valid,.faq_send_mail_success,.faq_send_mail_error').addClass('hidden');
    var checkError = true;
    if ($('#faq_name').length > 0 && $('#faq_name').val() == '') {
        $('.faq_name-alert').removeClass('hidden');
        checkError = false;
    }
    if ($('#faq_email').length > 0 && $('#faq_email').val() == '') {
        $('.faq_email-alert').removeClass('hidden');
        checkError = false;
    }
    if ($('#your_question').length > 0 && $('#your_question').val() == '') {
        $('.faq_your_question-alert').removeClass('hidden');
        checkError = false;
    }
    if ($('#faq_captcha').length > 0 && $('#faq_captcha').val() == '') {
        $('.captcha-alert').removeClass('hidden');
        checkError = false;
    }
    if ($('#ets_faq_g_recaptcha #g-recaptcha-response').length > 0 && $('#ets_faq_g_recaptcha #g-recaptcha-response').val() == '') {
        $('.captcha-alert').removeClass('hidden');
        checkError = false;
    }
    if (!$('#faq_email').length || !$('#faq_name').length || !$('#your_question').length)
        checkError = false;
    if (!$(this).hasClass('active') && checkError) {
        $('#faq_send_mail .faq_loading').removeClass('hidden');
        $(this).addClass('active');
        $('.faq-alert').remove();
        var formData = new FormData($(this).parents('form').get(0));
        $.ajax({
            url: faqBaseModuleUrl,
            dataType: 'json',
            type: 'post',
            processData: false,
            contentType: false,
            data: formData,
            success: function (json) {
                var json_success = true;
                $('#faq_send_mail .faq_loading').addClass('hidden');
                $('#faq_send_mail').removeClass('active');
                if (typeof json.error_faq_name !== 'undefined' && json.error_faq_name == true) {
                    $('.faq_name-valid').removeClass('hidden');
                    json_success = false;
                }
                if (typeof json.error_faq_email !== 'undefined' && json.error_faq_email == true) {
                    $('.faq_email-valid').removeClass('hidden');
                    json_success = false;
                }
                if (typeof json.error_faq_phone !== 'undefined' && json.error_faq_phone == true) {
                    $('.faq_phone-valid').removeClass('hidden');
                    json_success = false;
                }
                if (typeof json.error_faq_your_question !== 'undefined' && json.error_faq_your_question == true) {
                    $('.faq_your_question-valid').removeClass('hidden');
                    json_success = false;
                }
                if (typeof json.error_captcha !== 'undefined' && json.error_captcha == true) {
                    $('.captcha-valid').removeClass('hidden');
                    json_success = false;
                }
                if (typeof json.sendmail !== 'undefined' && json_success == true) {
                    if (json.sendmail == true) {
                        $('.faq_send_mail_success').removeClass('hidden');
                        if ($('#your_question').length > 0)
                            $('#your_question').val('');
                    } else
                        $('.faq_send_mail_error').removeClass('hidden');
                    if ($('#faq_captcha').length > 0 && $('#faq_captcha_refesh').length > 0) {
                        $('#faq_captcha').val('');
                        $('#faq_captcha_refesh').trigger('click');
                    }
                }
                ets_faq_resset_captcha();
            },
            error: function (xhr, status, error) {
                $('#faq_send_mail').removeClass('active');
                $('#faq_captcha_refesh').trigger('click');
                $('#faq_send_mail .faq_loading').addClass('hidden');
                $('.faq_send_mail_error').removeClass('hidden');
            }
        });
    } else {
        return checkError;
    }

});
