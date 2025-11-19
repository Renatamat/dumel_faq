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
    var faqBaseModuleUrl = '{if isset($action) && $action}{$action nofilter}{/if}';
</script>
<div class="faq_forms faq_overlay {if isset($layout) && $layout}{$layout|escape:'html':'UTF-8'}{/if}">
    <div class="faq_ask_a_question_form {if isset($layout) && $layout}{$layout|escape:'html':'UTF-8'}{/if} faq_pop_up">
        {if isset($layout) && $layout}
            <div data-title="&#xE14C;" class="faq_close">{l s='Close' mod='ets_faq'}</div>
        {/if}
        <div class="faq_form">
            <form id="form_ask_a_question" class="defaultForm form-horizontal"
                  action="{if isset($action) && $action}{$action|escape:'quotes':'UTF-8'}{/if}"
                  enctype="multipart/form-data" method="post">
                {if isset($configs.ETS_FAQ_FORM_ASK_QUESTION_TITLE) && $configs.ETS_FAQ_FORM_ASK_QUESTION_TITLE}
                    <div class="form-group">
                        <span class="title_block">{$configs.ETS_FAQ_FORM_ASK_QUESTION_TITLE|escape:'html':'UTF-8'}</span>
                    </div>
                {/if}
                {if isset($configs.ETS_FAQ_FORM_ASK_QUESTION_DESCRIPTION) && $configs.ETS_FAQ_FORM_ASK_QUESTION_DESCRIPTION}
                    <div class="form-group">
                        <span class="ask_a_question_desc">{$configs.ETS_FAQ_FORM_ASK_QUESTION_DESCRIPTION nofilter}</span>
                    </div>
                {/if}
                <div class="form-group">
                    <label class="" for="faq_name"><sub>*</sub> {l s='Name' mod='ets_faq'}</label>
                    <input class="form-control" type="text" id="faq_name" name="faq_name" value="{if isset($customer_info.name) && $customer_info.name}{$customer_info.name|escape:'html':'UTF-8'}{/if}"/>
                    <p class="faq_name-alert alert alert-danger hidden">{l s='Name is required' mod='ets_faq'}</p>
                    <p class="faq_name-valid alert alert-danger hidden">{l s='Name is invalid' mod='ets_faq'}</p>
                </div>
                <div class="form-group">
                    <label class="" for="faq_phone">{l s='Phone' mod='ets_faq'}</label>
                    <input class="form-control" type="text" id="faq_phone" name="faq_phone" value="{if isset($customer_info.phone) && $customer_info.phone}{$customer_info.phone|escape:'html':'UTF-8'}{/if}"/>
                    <p class="faq_phone-alert alert alert-danger hidden">{l s='Phone is required' mod='ets_faq'}</p>
                    <p class="faq_phone-valid alert alert-danger hidden">{l s='Phone is invalid' mod='ets_faq'}</p>
                </div>
                <div class="form-group">
                    <label class="" for="faq_email"><sub>*</sub> {l s='Email' mod='ets_faq'}</label>
                    <input class="form-control" type="text" id="faq_email" name="faq_email" value="{if isset($customer_info.email) && $customer_info.email}{$customer_info.email|escape:'html':'UTF-8'}{/if}"/>
                    <p class="faq_email-alert alert alert-danger hidden">{l s='Email is required' mod='ets_faq'}</p>
                    <p class="faq_email-valid alert alert-danger hidden">{l s='Email is invalid' mod='ets_faq'}</p>
                </div>
                <div class="form-group">
                    <label class="" for="faq_your_question"><sub>*</sub> {l s='Your question' mod='ets_faq'}</label>
                    <textarea class="form-control" id="your_question" name="faq_your_question"
                              style="height:150px;"></textarea>
                    <p class="faq_your_question-alert alert alert-danger hidden">{l s='Your question is required' mod='ets_faq'}</p>
                    <p class="faq_your_question-valid alert alert-danger hidden">{l s='Your question is invalid' mod='ets_faq'}</p>
                </div>
                {if isset($id_product) && $id_product}
                    <input name="id_product" value="{$id_product|intval}" type="hidden"/>
                {/if}
                {if isset($configs.ETS_FAQ_ENABLE_CAPTCHA_ASK_QUESTION_FROM) && $configs.ETS_FAQ_ENABLE_CAPTCHA_ASK_QUESTION_FROM}
                    <div class="form-group">
                        {hook h='displayFAQCaptcha'}
                    </div>
                {/if}
                <div class="footer">
                    <p class="faq_send_mail_success alert alert-success hidden">{l s='Your email has been sent successfully.' mod='ets_faq'}</p>
                    <p class="faq_send_mail_error alert alert-danger hidden">{l s='An error occurred while sending the message.' mod='ets_faq'}</p>
                    <button id="faq_send_mail" type="button" class="btn btn-default btn-primary">
                        <img class="faq_loading hidden" src="{$img_base_dir|escape:'quotes':'UTF-8'}loading.png" title="{l s='Send your question' mod='ets_faq'}" alt="..."/>
                        <span>{l s='Send your question' mod='ets_faq'}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>