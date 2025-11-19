/**
 * Simplified front interactions for the FAQ module.
 */
$(document).ready(function () {
    if ($('.faq_group_ul > li').length > 0) {
        $('.faq_group_ul > li:first-child').addClass('open');
    }
    if ($('.faq_tab_content > .faq_tab_pane').length > 0) {
        $('.faq_tab_content > .faq_tab_pane:first-child').addClass('open');
    }
});

$(document).on('click', '.faq_nav_link', function (e) {
    e.preventDefault();
    $('.faq_group_ul > li').removeClass('open');
    $('.faq_tab_content > .faq_tab_pane').removeClass('open');
    $(this).parent('li').addClass('open');
    $($(this).attr('ruler')).addClass('open');
});

$(document).on('click', '.faq_question_name', function (e) {
    e.preventDefault();
    $(this).toggleClass('open');
    $(this).next('.faq_answer').toggleClass('open');
});
