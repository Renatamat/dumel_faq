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
        $faqConfigs = $this->module->getFaqConfigs();
        if (Tools::getIsset('controller') && ($page = Tools::strtolower(trim(Tools::getValue('controller')))) && $page == 'faqs') {
            $context = Ets_faq::getInstanceContext();
            $page_rewrite = Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) ? Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) : $this->module->l('faqs', 'faqs');
            if ($page_rewrite && Configuration::get('PS_REWRITING_SETTINGS') && Tools::strpos($_SERVER['REQUEST_URI'],'/module/ets_faq') !== false) {
                Tools::redirect($this->module->getLink());
            }
        }
        $this->setMetas();
        $this->context->smarty->assign(array(
            'path' => $this->module->getBreadCrumb(),
            'breadcrumb' => $this->module->getBreadCrumb(),
            'configs' => $faqConfigs,
            'faqs' => $this->module->getFaqs(true),
        ));
        $this->setTemplate('module:ets_faq/views/templates/front/faqlist.tpl');
    }

    public function setMetas()
    {
        $meta = $this->module->getMetaTag($this->context->language->id);
        $page = $this->getTemplateVarPage();
        $page['meta']['title'] = $meta['meta_title'];
        $page['meta']['description'] = $meta['meta_description'];
        $page['meta']['keywords'] = $meta['meta_keywords'];
        $this->context->smarty->assign(array('page' => $page));
    }
}