
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
{extends file='page.tpl'}

{block name="page_content"}
    {if isset($faqs) && $faqs}
        <div class="faq_content">
            <h1 class="h1 title_block">{l s='Faqs' mod='ets_faq'}</h1>
            <div class="faq_list">
                    <ul class="faq_group_ul front_group_ul">
                        {foreach from=$faqs item='group'}
                            {if isset($group.questions) && $group.questions}
                                <li class="faq_group_li front_group_li" data-group="{$group.id_faq_group|intval}">
                                    <span class="faq_nav_link"
                                          ruler="#group{$group.id_faq_group|intval}">{$group.group_name|escape:'html':'UTF-8'}</span>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    <div class="faq_tab_content tab-content">
                        {foreach from=$faqs item='group'}
                            <div id="group{$group.id_faq_group|intval}" class="faq_tab_pane"
                                 data-group="{$group.id_faq_group|intval}">
                                {if isset($group.questions) && $group.questions}
                                    <ul class="ul_question_ul front_question_ul">
                                        {foreach from=$group.questions item='question'}
                                            <li class="faq_question_li front_question_li">
                                                <span class="faq_question_name front_question_name {if isset($configs.ETS_FAQ_OPEN_ALL_ANSWERS_ON_FAQ_PAGE) && $configs.ETS_FAQ_OPEN_ALL_ANSWERS_ON_FAQ_PAGE}open{/if}">{$question.question|escape:'html':'UTF-8'}</span>
                                                <div class="faq_answer front_answer {if isset($configs.ETS_FAQ_OPEN_ALL_ANSWERS_ON_FAQ_PAGE) && $configs.ETS_FAQ_OPEN_ALL_ANSWERS_ON_FAQ_PAGE}open{/if}">{$question.answer nofilter}</div>
                                            </li>
                                        {/foreach}
                                    </ul>
                                {/if}
                            </div>
                        {/foreach}
                    </div>
            </div>
        </div>
    {else}
        <div class="alert alert-warning faq_no_faqs"><span
                    class="no_lookbook">{l s='No faq available' mod='ets_faq'}</span></div>
    {/if}
{/block}