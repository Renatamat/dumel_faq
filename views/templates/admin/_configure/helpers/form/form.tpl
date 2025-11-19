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
{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'checkbox'}
        {$smarty.block.parent}
        {if isset($input.values.query) && $input.values.query}
            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]|escape:'html':'UTF-8'}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
                    {strip}
                        <label for="{$id_checkbox|escape:'html':'UTF-8'}">
                            <input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]"
                                   id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value.value)} value="{$value.value|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value.value,$fields_value[$input.name])} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'html':'UTF-8'}
                        </label>
                    {/strip}
                </div>
            {/foreach}
        {/if}
    {elseif $input.type == "search"}
        {$smarty.block.parent}
        <div class="form-group">
            <div class="col-lg-9">
                <div class="faq_search_product">
                    <input type="text"
                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                           name="{if isset($input.name)}{$input.name|escape:'html':'UTF-8'}{/if}"
                           placeholder="{if isset($input.placeholder)}{$input.placeholder|escape:'html':'UTF-8'}{/if}"
                           autocomplete="false"/>
                    <i class="icon-search"></i>
                </div>
                <ul class="faq-product-list">
                    {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name]}
                        {foreach from = $fields_value[$input.name] item='product'}
                            <li><img src="{$product.image_link|escape:'quotes':'UTF-8'}"
                                     alt="{$product.name|escape:'html':'UTF-8'}"/><span
                                        class="product-name">{$product.name|escape:'html':'UTF-8'}</span><span
                                        class="delete-product-id" data-id="{$product.id_product|intval}"><i
                                            class="icon-remove"></i></span></li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    {elseif $input.type == 'text' || $input.type == 'tags'}
        {if isset($input.lang) AND $input.lang}
        {if $languages|count > 1}
            <div class="form-group">
                {/if}
                {foreach $languages as $language}
                    {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        <div class="col-lg-10">
                    {/if}
                    {if $input.type == 'tags'}
                    {literal}
                        <script type="text/javascript">
                            $().ready(function () {
                                var input_id = '{/literal}{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}{literal}';
                                $('#' + input_id).tagify({
                                    delimiters: [13, 44],
                                    addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'
                                });
                                $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit(function () {
                                    $(this).find('#' + input_id).val($('#' + input_id).tagify('serialize'));
                                });
                            });
                        </script>
                    {/literal}
                    {/if}
                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                    <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                {/if}
                    {if isset($input.maxchar) && $input.maxchar}
                        <span id="{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"
                              class="input-group-addon">
													<span class="text-count-down">{$input.maxchar|intval}</span>
												</span>
                    {/if}
                    {if isset($input.prefix)}
                        <span class="input-group-addon">
													  {$input.prefix nofilter}
													</span>
                    {/if}
                    <input type="text"
                           id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"
                           name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}"
                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                           value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                           onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                            {if isset($input.size)} size="{$input.size|intval}"{/if}
                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                            {if isset($input.required) && $input.required} required="required" {/if}
                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
                    {if isset($input.link_rewrite) && $input.link_rewrite}
                        <span style="display:block; font-style: italic; margin-top: 5px;color:#959595;">{l s='FAQ page on your site:' mod='ets_faq'}
                            <a target="_blank" class="preview_link"
                               ref="{$input.link_rewrite[$language.id_lang]|escape:'quotes':'UTF-8'}"
                               href="{$input.link_rewrite[$language.id_lang]|escape:'quotes':'UTF-8'}{if $PS_REWRITING_SETTINGS}{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}{/if}"
                               title="{l s='Preview' mod='ets_faq'}">
                                {$input.link_rewrite[$language.id_lang]|escape:'quotes':'UTF-8'}{if $PS_REWRITING_SETTINGS}{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}{/if}
                            </a>
                        </span>
                        <span style="font-style: italic;color:#959595;">{l s='You can copy this link and paste to your menu module' mod='ets_faq'}</span>
                    {/if}
                    {if isset($input.suffix)}
                        <span class="input-group-addon">
													  {$input.suffix|escape:'html':'UTF-8'}
													</span>
                    {/if}
                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                    </div>
                {/if}
                    {if $languages|count > 1}
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1"
                                    data-toggle="dropdown">
                                {$language.iso_code|escape:'html':'UTF-8'}
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});"
                                           tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}
                {if isset($input.maxchar) && $input.maxchar}
                    <script type="text/javascript">
                        $(document).ready(function () {
                            {foreach from=$languages item=language}
                            countDown($("#{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
                            {/foreach}
                        });
                    </script>
                {/if}
                {if in_array($input.name, array("rewrite_url", "ETS_FAQ_REWRITE_URL"))}
                    <script type="text/javascript">
                        {if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
                        var PS_ALLOW_ACCENTED_CHARS_URL = 1;
                        {else}
                        var PS_ALLOW_ACCENTED_CHARS_URL = 0;
                        {/if}
                        var PS_REWRITING_SETTINGS = {$PS_REWRITING_SETTINGS|intval};
                    </script>
                {/if}
                {if $languages|count > 1}
            </div>
        {/if}
        {else}
            {if $input.type == 'tags'}
            {literal}
                <script type="text/javascript">
                    $().ready(function () {
                        var input_id = '{/literal}{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
                        $('#' + input_id).tagify({
                            delimiters: [13, 44],
                            addTagPrompt: '{/literal}{l s='Add tag'}{literal}'
                        });
                        $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit(function () {
                            $(this).find('#' + input_id).val($('#' + input_id).tagify('serialize'));
                        });
                    });
                </script>
            {/literal}
            {/if}
            {assign var='value_text' value=$fields_value[$input.name]}
        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                {/if}
                {if isset($input.maxchar) && $input.maxchar}
                    <span id="{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"
                          class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
                {/if}
                {if isset($input.prefix)}
                    <span class="input-group-addon">
										  {$input.prefix nofilter}
										</span>
                {/if}
                <input type="text"
                       name="{$input.name|escape:'html':'UTF-8'}"
                       id="{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                       value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                       class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                        {if isset($input.size)} size="{$input.size|intval}"{/if}
                        {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                        {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                        {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                        {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                        {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                        {if isset($input.required) && $input.required } required="required" {/if}
                        {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
                />
                {if isset($input.suffix)}
                    <span class="input-group-addon">
										  {$input.suffix|escape:'html':'UTF-8'}
										</span>
                {/if}

                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            </div>
        {/if}
            {if isset($input.maxchar) && $input.maxchar}
                <script type="text/javascript">
                    $(document).ready(function () {
                        countDown($("#{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
                    });
                </script>
            {/if}
        {/if}
	{else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="field"}
    {if $input.name}
        {$smarty.block.parent}
        {if $input.type == 'file' &&  isset($input.display_img) && $input.display_img}
            <label class="control-label col-lg-3 uploaded_image_label"
                   style="font-style: italic;">{l s='Uploaded image: ' mod='ets_faq'}</label>
            <div class="col-lg-9 uploaded_img_wrapper">
                <img title="{l s='Click to see full size image' mod='ets_faq'}"
                     style="display: inline-block; max-width: 200px;" src="{$input.display_img|escape:'html':'UTF-8'}"/>
                {if (!isset($input.hide_delete) || isset($input.hide_delete) && !$input.hide_delete) && isset($input.img_del_link) && $input.img_del_link && !(isset($input.required) && $input.required)}
                    <a class="delete_url" style="display: inline-block; text-decoration: none!important;"
                       href="{$input.img_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i
                                    style="font-size: 20px;" class="process-icon-delete"
                                    data-title="&#xE872;"></i></span></a>
                {/if}
            </div>
        {/if}
    {/if}
{/block}

{block name="footer"}
    {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
    {if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
        <div class="panel-footer">
            {if isset($cancel_url) && $cancel_url}
                <a class="btn btn-default" href="{$cancel_url|escape:'quotes':'UTF-8'}"><i
                            class="process-icon-cancel"></i>{l s='Back'  mod='ets_faq'}</a>
            {/if}
            {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
                <div class="img_loading_wrapper hidden">
                    <img src="{$image_baseurl|escape:'quotes':'UTF-8'}ajax-loader.gif"
                         title="{l s='Loading' mod='ets_faq'}" class="faq_loading"/>
                </div>
                <input type="hidden" name="faq_object" value="{$faq_object|escape:'html':'UTF-8'}"/>
                {if isset($list_item) && $list_item}
                    <input type="hidden" name="itemId" value="{$item_id|intval}"/>
                    <input type="hidden" name="faq_form_submitted" value="1"/>
                {else}
                    <input type="hidden" name="faq_config_submitted" value="1"/>
                {/if}
                <div class="faq_save_wrapper">
                    <button type="submit" value="1"
                            class="{if isset($list_item) && $list_item}faq_save{else}faq_config_save{/if} {if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'html':'UTF-8'}{else}btn btn-default pull-right{/if}">
                        <i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'html':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'html':'UTF-8'}
                    </button>
                </div>
            {/if}

        </div>
    {/if}
{/block}
{block name="input_row"}
    <div class="form-group-wrapper row_{strtolower($input.name)|escape:'html':'UTF-8'}">
        {$smarty.block.parent}
    </div>
{/block}
{block name="label"}
    {if isset($input.showRequired) && $input.showRequired}
        <label class="control-label col-lg-3 required">
            {$input.label|escape:'html':'UTF-8'}
            {*if isset($input.desc) && $input.desc}
                <div class="ets-help-block ets_tooltip">
                    <span class="help-question label-tooltip" data-placement="top" data-html="true" data-original-title="{$input.desc|escape:'html':'UTF-8'}" title="{$input.desc|escape:'html':'UTF-8'}" data-toggle="tooltip">?</span>
                </div>
            {/if*}
        </label>
    {else}
        {$smarty.block.parent} 
    {/if}
{/block}    