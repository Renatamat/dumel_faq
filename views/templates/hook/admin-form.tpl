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
<script type="text/javascript">
    var faqBaseAdminUrl = '{$faqBaseAdminUrl|escape:'quotes':'UTF-8'}';
    var ets_faq_invalid_file = '{l s='Image is invalid' mod='ets_faq' js=true}';
    var url_base_img = '{$url_base_img|escape:'quotes':'UTF-8'}';
    var id_lang ={$id_lang|intval};
    var faq_msg_delete ='{$faq_msg_delete|escape:'html':'UTF-8'}';
</script>
<div class="ets_faq_wrapper faq_desktop_size">
    <div class="ets_faq">
        <div class="faq_loading"><img src="{$url_base_img|escape:'quotes':'UTF-8'}ajax-loader.gif"></div>
        <div class="faq_content">
            <div data-title="&#xE8B8;" class="faq_config_button btn btn-default"><i
                        class="icon-cog"></i> {l s='Configuration' mod='ets_faq'}</div>
            <div class="faq_blank"></div>
            <div class="faq_group_tab" {hook h='displayFAQConfigs'}>
                {hook h='displayFAQGroupTabs'}
                <div data-title="&#xE147;" class="faq_add_group btn btn-default"><i class="icon-plus"></i> {l s='Add group' mod='ets_faq'}</div>
            </div>
            <div class="faq_group_list" {hook h='displayFAQConfigs'}>
                {hook h='displayFAQGroupLists'}
            </div>
        </div>
    </div>
    <div class="faq_forms faq_overlay hidden">
        <div class="faq_group_form hidden faq_pop_up">
            <div data-title="&#xE14C;" class="faq_close">{l s='Close' mod='ets_faq'}</div>
            <div class="faq_form"></div>
        </div>
        <div class="faq_group_form_new hidden">{$groupForm nofilter}</div>
    </div>
    <div class="faq_overlay hidden">
        <div class="faq_config_form hidden faq_pop_up">
            <div data-title="&#xE14C;" class="faq_close">{l s='Close' mod='ets_faq'}</div>
            <div class="faq_config_form_content">
                <div class="faq_close"></div>{$configForm nofilter}</div>
        </div>
    </div>
</div>