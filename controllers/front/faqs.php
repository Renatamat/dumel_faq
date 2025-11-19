<?php
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

if (!defined('_PS_VERSION_')) { exit; }

/**
 * Class Ets_faqFaqsModuleFrontController
 * @property Ets_faq $module
 */
class Ets_faqFaqsModuleFrontController extends ModuleFrontController
{
    public $template = 'faqlist.tpl';
    /**
     * @var Ets_faq
     */
    public $module;
    public function initContent()
    {
        parent::initContent();
        $context = Ets_faq::getInstanceContext();
        $faq_configs = $this->module->getFaqConfigs();
        if (Tools::getIsset('send_mail') && Tools::getValue('send_mail') == 1) {
            $assgin = array();
            $vardata = array(
                'faq_name' => Tools::getValue('faq_name') ? Tools::getValue('faq_name') : '',
                'faq_email' => Tools::getValue('faq_email') ? Tools::getValue('faq_email') : '',
                'faq_phone' => Tools::getValue('faq_phone') ? Tools::getValue('faq_phone') : '',
                'id_product' => Tools::getValue('id_product'),
                'faq_your_question' => Tools::getValue('faq_your_question') ? Tools::getValue('faq_your_question') : '',
            );
            if (!Validate::isName($vardata['faq_name'])) {
                $assgin['error_faq_name'] = true;
            }
            if (!Validate::isPhoneNumber($vardata['faq_phone'])) {
                $assgin['error_faq_phone'] = true;
            }
            if (!Validate::isEmail($vardata['faq_email'])) {
                $assgin['error_faq_email'] = true;
            }
            if (!Validate::isCleanHtml($vardata['faq_your_question'])) {
                $assgin['error_faq_your_question'] = true;
            }
            if ($this->module->checkUserCaptcha()) {
                if($faq_configs['ETS_FAQ_CAPTCHA_TYPE']=='image')
                {
                    $captcha_faq = Tools::getValue('faq_captcha') ? Tools::getValue('faq_captcha') : false;
                    if ($captcha_faq && $context->cookie->__get('security_faq') && $captcha_faq === $context->cookie->__get('security_faq')) {
                        $alerts = $this->module->sendMessage($vardata);
                        $assgin['error_captcha'] = false;
                        $assgin['sendmail'] = $alerts;
                    } else {
                        $assgin['error_captcha'] = true;
                    }
                }
                elseif($faq_configs['ETS_FAQ_CAPTCHA_TYPE']=='google' || $faq_configs['ETS_FAQ_CAPTCHA_TYPE']=='google_v3')
                {
                    $recaptcha = Tools::getValue('g-recaptcha-response') ? Tools::getValue('g-recaptcha-response') : '';
                    $secret = $faq_configs['ETS_FAQ_CAPTCHA_TYPE']=='google' ? Configuration::get('ETS_FAQ_GOOGLE_CAPTCHA_SECRET_KEY') : Configuration::get('ETS_FAQ_GOOGLE_CAPTCHA_SECRET_KEY3');
                    $link_captcha="https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptcha . "&remoteip=" . Tools::getRemoteAddr();
                    if(!$recaptcha)
                    {
                        $assgin['error_captcha'] = true;
                    }
                    else
                    {
                        $recaptcha = Tools::getValue('g-recaptcha-response') ? Tools::getValue('g-recaptcha-response') : false;
                        if ($recaptcha) {
                            $response = json_decode(Tools::file_get_contents($link_captcha), true);
                            if ($response['success'] == false) {
                                $assgin['error_captcha'] = true;
                            }
                            else
                            {
                                $alerts = $this->module->sendMessage($vardata);
                                $assgin['error_captcha'] = false;
                                $assgin['sendmail'] = $alerts;
                            }
                        }
                    }
                }
                
            } else {
                $alerts = $this->module->sendMessage($vardata);
                $assgin['sendmail'] = $alerts;
            }
            die(json_encode($assgin));

        }
        if (Tools::getIsset('controller') && ($page = Tools::strtolower(trim(Tools::getValue('controller')))) && $page == 'faqs') {
            $context = Ets_faq::getInstanceContext();
            $page_rewrite = Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) ? Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) : $this->module->l('faqs', 'faqs');
            if ($page_rewrite && Configuration::get('PS_REWRITING_SETTINGS') && Tools::strpos($_SERVER['REQUEST_URI'],'/module/ets_faq') !==false ) {
                Tools::redirect($this->module->getLink());
            }
            $this->setMetas();
            $this->context->smarty->assign(array(
                'path' => $this->module->getBreadCrumb(),
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'configs' => $this->module->getFaqConfigs(),
                'faqs' => $this->module->getFaqs(true),
            ));
            $this->template = $this->module->is17 ? 'module:ets_faq/views/templates/front/faqlist.tpl' : 'faqlist16.tpl';
        }
        $this->setTemplate($this->template);
    }

    public function setMetas()
    {
        $meta = $this->module->getMetaTag($this->context->language->id);
        if ($this->module->is17) {
            $page = $this->getTemplateVarPage();
            $page['meta']['title'] = $meta['meta_title'];
            $page['meta']['description'] = $meta['meta_description'];
            $page['meta']['keywords'] = $meta['meta_keywords'];
            $templateVars = array(
                'page' => $page
            );
            $this->context->smarty->assign($templateVars);
        } else {
            $this->context->smarty->assign($meta);
        }
    }
}