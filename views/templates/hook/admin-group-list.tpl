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
{if isset($group) && $group}
    <li class="faq_group_li item{$group.id_faq_group|intval}" data-id-faq_group="{$group.id_faq_group|intval}"
        data-obj="group">
        <div class="action">
            <span data-title="&#xE14C;" class="faq_group_add_question" title="{l s='Add question' mod='ets_faq'}"><i
                        class="icon-plus" aria-hidden="true"></i> {l s='Add question' mod='ets_faq'}</span>
            <span data-title="&#xE14C;"
                  class="faq_group_delete {if isset($group.id_faq_group) && $group.id_faq_group == 1}hidden{/if}"
                  title="{l s='Delete group' mod='ets_faq'}"><i class="icon-trash"
                                                                    aria-hidden="true"></i> {l s='Delete group' mod='ets_faq'}</span>
            <span data-title="&#xE150;" class="faq_group_edit" title="{l s='Edit group' mod='ets_faq'}"> <i
                        class="icon-pencil" aria-hidden="true"></i> {l s='Edit group' mod='ets_faq'}</span>
        </div>
        <div class="group-content">
            {hook h='displayFAQQuestionGroup' questions = $group.questions id_faq_group = $group.id_faq_group}
        </div>
    </li>
{/if}