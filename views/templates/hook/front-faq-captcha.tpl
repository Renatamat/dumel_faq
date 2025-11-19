{*
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
*}

<div class="form-group">
    <label for="faq_captcha"><sub>*</sub> {l s='Enter security code' mod='ets_faq'}</label>
    <span class="faq_captcha_image">
        <img class="faq_captcha_image_data" src="{$captcha_image|escape:'html':'UTF-8'}"
             alt="{l s='Security code' mod='ets_faq'}" title="{l s='Security code' mod='ets_faq'}"/>
        <span id="faq_captcha_refesh" data-rand="{$rand|escape:'html':'UTF-8'}"><img
                    title="{l s='Refresh the code' mod='ets_faq'}" alt="{l s='Refresh the code' mod='ets_faq'}"
                    src="{$modules_dir|escape:'html':'UTF-8'}ets_faq/views/img/refresh.png"/></span>
    </span>
    <input type="text" class="form-control" name="faq_captcha" id="faq_captcha"/>
    <p class="captcha-alert alert alert-danger hidden">{l s='Captcha is required' mod='ets_faq'}</p>
    <p class="captcha-valid alert alert-danger hidden">{l s='Captcha is invalid' mod='ets_faq'}</p>
</div>