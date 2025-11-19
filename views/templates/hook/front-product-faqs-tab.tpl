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
{if isset($configs.ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT) && $configs.ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT}
    <li class="nav-item">
        <a
                class="nav-link"
                data-toggle="tab"
                href="#faqs"
                role="tab"
                aria-controls="faqs">{if isset($configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE) && $configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE}{$configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE|escape:'html':'UTF-8'}{else}{l s='FAQs' mod='ets_faq'}{/if}</a>
    </li>
{/if}