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
{if isset($question) && $question}
    <li class="faq_question_li item{$question.id_faq_question|intval}"
        data-id-faq_group="{$question.id_faq_group|intval}" data-id-faq_question="{$question.id_faq_question|intval}"
        data-obj="question">
        <div class="action">
            <span data-title="&#xE14C;" class="faq_question_delete" title="{l s='Delete question' mod='ets_faq'}"><i
                        class="icon-trash" aria-hidden="true"></i></span>
            <span data-title="&#xE150;" class="faq_question_edit" title="{l s='Edit question' mod='ets_faq'}"><i
                        class="icon-pencil" aria-hidden="true"></i></span>
        </div>
        <div class="question-content">
            <h3 class="title_block question_name">{$question.question|escape:'html':'UTF-8'}</h3>
            <div class="answer">{$question.answer nofilter}</div>
        </div>
    </li>
{/if}