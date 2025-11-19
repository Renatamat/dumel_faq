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
<div class="faq_question_wrapper group{if isset($id_faq_group)}{$id_faq_group|intval}{/if}">
    <ul class="faq_question_ul">
        {if isset($questions) && $questions}
            {foreach from=$questions item='question'}
                {hook h='displayFAQQuestion' question=$question}
            {/foreach}
        {/if}
    </ul>
    <div class="alert alert-warning faq_no_questions group{if isset($id_faq_group)}{$id_faq_group|intval}{/if} {if $questions}hidden{/if}">{l s='No question available. Click "Add question" to add new question.' mod='ets_faq'}</div>
</div>     