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
{if isset($faqs) && $faqs}
    <div class="faq_on_product">
        <div class="faqs_content">
            <h3 class="title_block">{if isset($configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE) && $configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE}{$configs.ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE|escape:'html':'UTF-8'}{else}{l s='FAQs' mod='ets_faq'}{/if}</h3>
            <ul class="faq_list">
                {foreach from=$faqs item='faq'}
                    <li class="faq_question_li item{$faq.id_faq_question|intval}">
                        <span class="faq_question_name">{$faq.question|escape:'html':'UTF-8'}</span>
                        <div class="faq_answer">{$faq.answer nofilter}</div>
                    </li>
                {/foreach}
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
{/if}