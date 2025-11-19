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
{if $tag_name}
<{$tag_name|escape:'html':'UTF-8'}
    {if $tag_class} class="{$tag_class|escape:'html':'UTF-8'}"{/if}
    {if $tag_id} id="{$tag_id|escape:'html':'UTF-8'}"{/if}
    {if $rel} rel="{$rel|escape:'html':'UTF-8'}"{/if}
    {if $type} type="{$type|escape:'html':'UTF-8'}"{/if}
    {if $data_id_product} data-id_product="{$data_id_product|escape:'html':'UTF-8'}"{/if}
    {if $value} value="{$value|escape:'html':'UTF-8'}"{/if}
    {if $href} href="{$href nofilter}"{/if}{if $tag_name=='a' && $blank} target="_blank"{/if}
    {if $tag_name=='img' && $src} src="{$src nofilter}"{/if}
    {if $attr_name} name="{$attr_name|escape:'html':'UTF-8'}"{/if}
    {if $attr_datas}
        {foreach from=$attr_datas item='data'}
            {$data.name|escape:'html':'UTF-8'}="{$data.value|escape:'html':'UTF-8'}"
        {/foreach}
    {/if}
    {if $tag_name=='img' || $tag_name=='br' || $tag_name=='input'} /{/if}
>
    {/if}{if $tag_name && $tag_name!='img' && $tag_name!='input' && $tag_name!='br' && !is_null($tag_content)}{$tag_content nofilter}{/if}{if $tag_name && $tag_name!='img' && $tag_name!='input' && $tag_name!='br'}</{$tag_name|escape:'html':'UTF-8'}>{/if}