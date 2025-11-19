{*
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
*}

{if $ETS_FAQ_CAPTCHA_TYPE=='google'}
    <script type="text/javascript">
        var ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY = '{$ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY|escape:'html':'UTF-8'}';
        var ets_faq_g_recaptchaonloadCallback = function() {
            ets_faq_g_recaptcha = grecaptcha.render(document.getElementById('ets_faq_g_recaptcha'), {
                'sitekey':ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY,
                'theme':'light'
            });
        };
    </script>
{else}
    <script src="https://www.google.com/recaptcha/api.js?render={$ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY3|escape:'html':'UTF-8'}"></script>
    <script type="text/javascript">
        var ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY = '{$ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY3|escape:'html':'UTF-8'}';
        {literal}
        var ets_faq_g_recaptchaonloadCallback = function() {
            grecaptcha.ready(function() {
                grecaptcha.execute(ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY, {action: 'homepage'}).then(function(token) {
                    $('#ets_faq_g_recaptcha').val(token);
                });
            });
        };
        {/literal}
    </script>
{/if}