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
require_once(dirname(__FILE__) . '/classes/FAQ_Obj.php');
require_once(dirname(__FILE__) . '/classes/FAQ_Group.php');
require_once(dirname(__FILE__) . '/classes/FAQ_Question.php');
require_once(dirname(__FILE__) . '/classes/FAQ_Config.php');
require_once(dirname(__FILE__) . '/classes/FAQ_Link.php');

class Ets_faq extends Module
{
    public static $groups;
    public static $questions;
    public static $trans;
    public static $configs;
    public static $position_hook = array();
    public $alerts;
    public $is17 = false;
    public $baseAdminPath;
    private $_html;

    public function __construct()
    {
        $this->name = 'ets_faq';
        $this->tab = 'front_office_features';
        $this->version = '1.1.3';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('FAQ PRO – Frequently asked questions');
        $this->description = $this->l('Create frequently asked questions (FAQ) page and product question tab with question form.');
$this->refs = 'https://prestahero.com/';
        $this->module_key = 'a07b2a104f0823a1cb3dd05cd4d8b9fc';
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->translates();

        if (version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;

        if ($this->is17) {
            self::$position_hook = array(
                array(
                    'id_option' => 'displayFooterProduct',
                    'name' => $this->l('Bottom of product page'),
                ),
                array(
                    'id_option' => 'displayAfterProductThumbs',
                    'name' => $this->l('Left product column'),
                ),
                array(
                    'id_option' => 'displayProductAdditionalInfo',
                    'name' => $this->l('Right product column'),
                ),
            );
        } else {
            self::$position_hook = array(
                array(
                    'id_option' => 'displayFooterProduct',
                    'name' => $this->l('Bottom of product page'),
                ),
                array(
                    'id_option' => 'displayProductTabContent',
                    'name' => $this->l('Product tab column'),
                ),
            );
        }
        self::$groups = array(
            'form' => array(
                'legend' => array(
                    'title' => (int)Tools::getValue('itemId') ? $this->l('Edit group') : $this->l('Add group'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'name' => 'group',
                'connect_to' => 'question',
            ),
            'configs' => array(
                'group_name' => array(
                    'label' => $this->l('Group name'),
                    'type' => 'text',
                    'required' => true,
                    'lang' => true,
                    'validate' => 'isString',
                ),
                'sort_order' => array(
                    'label' => $this->l('Sort order'),
                    'type' => 'sort_order',
                    'required' => true,
                    'default' => 1,
                    'order_group' => false,
                    'validate' => 'isUnsignedInt'
                ),
            ),
        );

        self::$questions = array(
            'form' => array(
                'legend' => array(
                    'title' => (int)Tools::getValue('id_faq_question') ? $this->l('Edit question') : $this->l('Add question'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'name' => 'question',
                'parent' => 'group',
            ),
            'configs' => array(
                'id_faq_group' => array(
                    'label' => $this->l('Group'),
                    'type' => 'hidden',
                    'default' => ($id_faq_group = (int)Tools::getValue('id_faq_group')) && $id_faq_group > 0 ? $id_faq_group : null,
                    'required' => true,
                    'validate' => 'isUnsignedInt',
                ),
                'question' => array(
                    'label' => $this->l('Question'),
                    'type' => 'text',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'validate' => 'isString',
                ),
                'answer' => array(
                    'label' => $this->l('Answer'),
                    'type' => 'textarea',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'validate' => 'isCleanHtml',
                ),
                'display_on_product' => array(
                    'label' => $this->l('Display on product'),
                    'type' => 'select',
                    'default'=>2,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 1,
                                'name' => $this->l('None')
                            ),
                            array(
                                'id_option' => 2,
                                'name' => $this->l('All products')
                            ),
                            array(
                                'id_option' => 3,
                                'name' => $this->l('Specific products')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'validate' => 'isUnsignedInt',
                ),
                'question_product' => array(
                    'label' => $this->l('Products'),
                    'type' => 'search',
                    'class' => 'auto_search_complete',
                    'showRequired' => true,
                    'placeholder' => $this->l('Search product by product id, name or reference'),
                ),
                'product_ids' => array(
                    'label' => $this->l('Product Ids'),
                    'type' => 'hidden',
                    'default' => Tools::getValue('id_faq_question') ? $this->getProductsInQuestion((int)Tools::getValue('id_faq_question')) : '',
                    'validate' => 'isString'
                ),
                'enabled' => array(
                    'label' => $this->l('Enabled'),
                    'type' => 'switch',
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'faq_enabled_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'faq_enabled_0',
                            'value' => 0,
                        )
                    ),
                    'validate' => 'isBool',
                ),
                'sort_order' => array(
                    'label' => $this->l('Sort order'),
                    'type' => 'sort_order',
                    'required' => true,
                    'default' => 1,
                    'order_group' => false,
                    'validate' => 'isUnsignedInt'
                ),
            ),
        );

        self::$configs = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-AdminAdmin'
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'name' => 'config'
            ),
            'configs' => array(
                'ETS_FAQ_META_TITLE' => array(
                    'type' => 'text',
                    'label' => $this->l('Listing page meta title'),
                    'rows' => 5,
                    'cols' => 100,
                    'lang' => true,
                    'required' => true,
                    'default' => 'faq',
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                'ETS_FAQ_META_DESCRIPTION' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Listing page meta description'),
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 100,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                'ETS_FAQ_META_KEYWORDS' => array(
                    'type' => 'tags',
                    'label' => $this->l('Keywords'),
                    'default' => $this->l('lorem,ipsum,dolor'),
                    'lang' => true,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}',
                    'desc' => $this->l('Separated by a comma (,)'),
                ),
                'ETS_FAQ_REWRITE_URL' => array(
                    'type' => 'text',
                    'label' => $this->l('Rewrite url'),
                    'lang' => true,
                    'required' => true,
                    'default' => 'faq',
                    'link_rewrite' => $this->getLinkRewrite(),
                    'validate' => 'isLinkRewrite',
                    'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
                ),
                'ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT' => array(
                    'label' => $this->l('Enable frequently asked questions on product page'),
                    'type' => 'switch',
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'faq_on_product_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'faq_on_product_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_TAB_TITLE_ON_PRODUCT_PAGE' => array(
                    'type' => 'text',
                    'label' => $this->l('Tab title'),
                    'lang' => true,
                    'default' => $this->l('FAQs')
                ),
                'ETS_FAQ_POSITION_ON_PRODUCT_PAGE' => array(
                    'type' => 'select',
                    'label' => $this->l('Position on product page'),
                    'options' => array(
                        'query' => self::$position_hook,
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                    'default' => 'displayFooterProduct',
                ),
                'ETS_FAQ_OPEN_ALL_ANSWERS_ON_FAQ_PAGE' => array(
                    'label' => $this->l('Open all answers on FAQ page'),
                    'type' => 'switch',
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'open_all_answer_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'open_all_answer_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_FORM_ASK_QUESTION_TITLE' => array(
                    'label' => $this->l('Title'),
                    'type' => 'text',
                    'required' => true,
                    'lang' => true,
                    'default' => $this->l('Ask a Question'),
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                'ETS_FAQ_FORM_ASK_QUESTION_DESCRIPTION' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'lang' => true,
                    'default' => $this->l('If you have any question, don\'t hesitate to ask us. We will answer as soon as possible'),
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}',
                    'validate' => 'isCleanHtml',
                ),
                'ETS_FAQ_SEND_QUESTION_TO_EMAIL' => array(
                    'type' => 'text',
                    'prefix' => ' ',
                    'label' => $this->l('Send question to email:'),
                    'required' => true,
                    'col' => '4',
                    'form_group_class' => 'send_question_email',
                    'autocomplete' => false,
                    'default' => Configuration::get('PS_SHOP_EMAIL') ? Configuration::get('PS_SHOP_EMAIL') : false,
                    'validate' => 'isEmail',
                ),
                'ETS_FAQ_ENABLE_ASK_QUESTION_ON_PAGE_FAQ' => array(
                    'label' => $this->l('Enable ask a question form on FAQ page'),
                    'type' => 'switch',
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'ask_question_on_faq_page_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'ask_question_on_faq_page_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_ENABLE_ASK_QUESTION_ON_PRODUCT_PAGE' => array(
                    'label' => $this->l('Enable ask a question form on product page'),
                    'type' => 'switch',
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'ask_question_on_product_page_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'ask_question_on_product_page_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_ENABLE_CAPTCHA_ASK_QUESTION_FROM' => array(
                    'label' => $this->l('Enable captcha on Ask a question form'),
                    'type' => 'switch',
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'captcha_on_ask_question_from_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'captcha_on_ask_question_from_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_NOT_REQUIRE_REGISTERED' => array(
                    'label' => $this->l('Do not require registered user to enter captcha code'),
                    'type' => 'switch',
                    'default' => 1,
                    'form_group_class' => 'captcha',
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'id' => 'ETS_FAQ_NOT_REQUIRE_REGISTERED_1',
                            'value' => 1,
                        ),
                        array(
                            'label' => $this->l('No'),
                            'id' => 'ETS_FAQ_NOT_REQUIRE_REGISTERED_0',
                            'value' => 0,
                        )
                    ),
                ),
                'ETS_FAQ_CAPTCHA_TYPE' => array(
                    'type' => 'select',
                    'label' => $this->l('Captcha type'),
                    'form_group_class' => 'captcha',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'image',
                                'name' => $this->l('Captcha image'),
                            ),
                            array(
                                'id_option'=> 'google',
                                'name' => $this->l('Google reCAPTCHA - V2')
                            ),
                            array(
                                'id_option'=> 'google_v3',
                                'name' => $this->l('Google reCAPTCHA - V3')
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                    'default' => 'image',
                ),
                'ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY' => array(
                    'type' => 'text',
                    'showRequired' => true,
                    'label' => $this->l('Site key'),
                    'form_group_class' => 'captcha captcha_type google',
                    'desc' => '<'.'a'.' href="'.'https://www.google.com/recaptcha/about/'.'">'.'https://www.google.com/recaptcha/about/'.'<'.'/'.'a'.'>',
                ),
                'ETS_FAQ_GOOGLE_CAPTCHA_SECRET_KEY' => array(
                    'type' => 'text',
                    'showRequired' => true,
                    'label' => $this->l('Secret key'),
                    'form_group_class' => 'captcha captcha_type google',
                ),
                'ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY3' => array(
                    'type' => 'text',
                    'showRequired' => true,
                    'label' => $this->l('Site key'),
                    'desc' => '<'.'a'.' href="'.'https://www.google.com/recaptcha/about/'.'">'.'https://www.google.com/recaptcha/about/'.'<'.'/'.'a'.'>',
                    'form_group_class' => 'captcha captcha_type google_v3',
                ),
                'ETS_FAQ_GOOGLE_CAPTCHA_SECRET_KEY3' => array(
                    'type' => 'text',
                    'showRequired' => true,
                    'label' => $this->l('Secret key'),
                    'form_group_class' => 'captcha captcha_type google_v3',
                ),
                
            ),
        );
    }

    public function translates()
    {
        self::$trans = array(
            'required_text' => $this->l('is required'),
            'data_saved' => $this->l('Saved'),
            'unkown_error' => $this->l('Unknown error happens'),
            'object_empty' => $this->l('Object is empty'),
            'field_not_valid' => $this->l('Field is not valid'),
            'file_too_large' => $this->l('Upload file cannot be large than 100MB'),
            'file_existed' => $this->l('File name already exists. Try to rename the file and upload again'),
            'can_not_upload' => $this->l('Cannot upload file'),
            'upload_error_occurred' => $this->l('An error occurred during the image upload process.'),
            'image_deleted' => $this->l('Image deleted'),
            'item_deleted' => $this->l('Item deleted'),
            'cannot_delete' => $this->l('Cannot delete the item due to an unknown technical problem'),
            'invalid_text' => $this->l('is invalid'),

            'content_required_text' => $this->l('Text content is required'),
            'link_required_text' => $this->l('Link is required'),
            'image_required_text' => $this->l('Image is required'),
            'layer_type_not_valid' => $this->l('Layer type is not valid'),
        );
    }

    public function getProductsInQuestion($id_faq_question = false)
    {
        $ids = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("
            SELECT fq.product_ids
            FROM " . _DB_PREFIX_ . "ets_faq_question fq
            WHERE 1 " . ($id_faq_question ? " AND id_faq_question=" . (int)$id_faq_question : "") . "
        ");
        if ($ids)
            return $ids;
        return '';
    }

    public function getLinkRewrite()
    {
        $context = $this->context;
        $tmp = array();
        $languages = Language::getLanguages(false);
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $lbLink = new FAQ_Link();
            foreach ($languages as $l)
                $tmp[$l['id_lang']] = $lbLink->getBaseLinkFriendly($context->shop->id, true, $context) . $lbLink->getLangLinkFriendly($l['id_lang'], $context, $context->shop->id);
            unset($l);
        } else {
            foreach ($languages as $l)
                $tmp[$l['id_lang']] = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'index.php?fc=module&module=' . $this->name . '&controller=faqs&id_lang=' . (int)$l['id_lang'];
            unset($l);
        }
        return $tmp;
    }

    public static function clearUploadedImages()
    {
        if (@file_exists(dirname(__FILE__) . '/views/img/upload/') && ($files = glob(dirname(__FILE__) . '/views/img/upload/*'))) {
            foreach ($files as $file)
                if (@file_exists($file) && strpos($file, 'index.php') === false)
                    @unlink($file);
        }
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        $config = new FAQ_Config();
        $config->installConfigs();

        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayFAQGroupTab')
            && $this->registerHook('displayFAQGroupTabs')
            && $this->registerHook('displayFAQGroupList')
            && $this->registerHook('displayFAQGroupLists')
            && $this->registerHook('displayFAQQuestion')
            && $this->registerHook('displayFAQQuestionGroup')
            && $this->registerHook('displayProductTabContent')
            && $this->registerHook('displayFAQCaptcha')
            && $this->registerHook('displayFAQAskAQuestionForm')
            && $this->registerHook('displayFAQConfigs')
            && $this->registerHook('displayFAQOnPageProduct')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('moduleRoutes')
            && $this->installDb();
    }

    public function installDb()
    {
        $dbExecuted =
            Db::getInstance()->execute("
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_faq_group` ( 
                `id_faq_group` int(11) NOT NULL AUTO_INCREMENT ,
                `sort_order` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_faq_group`)) ENGINE = InnoDB
            ")
            && Db::getInstance()->execute("
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_faq_group_lang` (
                  `id_faq_group` int(11) NOT NULL,
                  `id_lang` int(11) NOT NULL,
                  `id_shop` INT(11) NOT NULL,
                  `group_name` varchar(200) CHARACTER SET utf8 NOT NULL,                                 
                  PRIMARY KEY (`id_faq_group`,`id_lang`,`id_shop`)) ENGINE = InnoDB
            ")
            && Db::getInstance()->execute("
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_faq_question` (
                `id_faq_question` int(11) NOT NULL AUTO_INCREMENT,
                `id_faq_group` int(11) NOT NULL,
                `display_on_product` tinyint(1) NOT NULL,
                `product_ids` varchar(150) NOT NULL,
                `enabled` tinyint(1) NOT NULL,
                `sort_order` int(11) NOT NULL,
                PRIMARY KEY (`id_faq_question`)) ENGINE = InnoDB
            ")
            && Db::getInstance()->execute("
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_faq_question_lang` (
                `id_faq_question` int(11) NOT NULL,
				`id_lang` int(11) NOT NULL,
                `question` varchar(500) CHARACTER SET utf8 NOT NULL,
				`answer` text CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id_faq_question`,`id_lang`)) ENGINE = InnoDB
            ");
        $this->sampleData();
        return $dbExecuted;
    }

    public function sampleData()
    {
        $group = new FAQ_Group();
        $group->group_name = $this->l('General');
        $group->sort_order = 1;
        $group->add();
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDb();
    }

    public function uninstallDb()
    {
        return
            Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_faq_group`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_faq_group_lang`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_faq_question`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_faq_question_lang`");
    }

    public function getContent()
    {
        $this->proccessPost();
        $this->requestForm();
        $this->_html .= $this->displayAdminJs();
        $this->_html .= $this->renderForm();
        return $this->_html;
    }

    public function proccessPost()
    {
        $this->alerts = array();
        $time = time();

        /*@todo saveobj*/
        if (Tools::isSubmit('faq_form_submitted') && ($mmObj = Tools::getValue('faq_object')) && in_array($mmObj, array('FAQ_Group', 'FAQ_Question'))) {
            $obj = ($itemId = (int)Tools::getValue('itemId')) && $itemId > 0 ? new $mmObj($itemId) : new $mmObj();
            $this->alerts = $obj->saveData();
            $vals = $obj->getFieldVals();
            $processResult = array(
                'alert' => $this->displayAlerts($time),
                'itemId' => (int)$obj->id,
                'title' => property_exists($obj, 'title') && isset($obj->title[(int)$this->context->language->id]) ? $obj->title[(int)$this->context->language->id] : false,
                'images' => $obj->id && property_exists($obj, 'image') && $obj->image ? array(array(
                    'name' => 'image',
                    'url' => $this->_path . 'views/img/upload/' . $obj->image,
                )) : false,
                'itemKey' => 'id_faq_' . $obj->fields['form']['name'],
                'time' => $time,
                'faq_object' => $mmObj,
                'vals' => $vals,
                'success' => isset($this->alerts['success']) && $this->alerts['success'],
            );
            if ($mmObj == 'FAQ_Question' && (int)$obj->id) {
                $question = $this->getQuestions(false, false, (int)$obj->id);
                $processResult['questionHtml'] = $this->hookDisplayFAQQuestion(array('question' => $question));
            }
            if ($mmObj == 'FAQ_Group' && (int)$obj->id) {
                $processResult['grouptab'] = $this->hookDisplayFAQGroupTab(array('group' => $this->getGroupFaqs($obj->id)));
                $processResult['grouplist'] = $this->hookDisplayFAQGroupList(array('group' => $this->getFaqs(false, $obj->id)));
            }
            die(json_encode($processResult));
        }

        /*@todo deleteobj*/
        if (Tools::getValue('deleteobject') && ($mmObj = Tools::getValue('faq_object')) && in_array($mmObj, array('FAQ_Group', 'FAQ_Question')) && ($itemId = (int)Tools::getValue('itemId'))) {
            $obj = new $mmObj($itemId);
            $this->alerts = $obj->deleteObj();
            $processResult = array(
                'alert' => $this->displayAlerts($time),
                'itemId' => (int)$itemId,
                'time' => $time,
                'faq_object' => $mmObj,
                'success' => isset($this->alerts['success']) && $this->alerts['success'],
                'successMsg' => isset($this->alerts['success']) && $this->alerts['success'] ? $this->l('Item deleted') : false,
            );
            die(json_encode($processResult));
        }

        /*@todo save ajax config*/
        if (Tools::isSubmit('faq_config_submitted')) {
            $config = new FAQ_Config();
            $this->alerts = $config->saveData();
            die(json_encode(array(
                'alert' => $this->displayAlerts($time),
                'time' => $time,
                'success' => isset($this->alerts['success']) && $this->alerts['success'],
                'configs' => $this->getFaqConfigs(true),
            )));
        }

        if (Tools::isSubmit('updateOrder')) {
            $itemId = (int)Tools::getValue('itemId');
            $objName = 'FAQ_' . Tools::ucfirst(Tools::strtolower(trim(Tools::getValue('obj'))));
            $previousId = (int)Tools::getValue('previousId');
            $result = false;
            if (in_array($objName, array('FAQ_Question', 'FAQ_Group')) && $itemId > 0) {
                $obj = new $objName($itemId);
                $result = $obj->updateOrder($previousId);
            }
            die(json_encode(array(
                'success' => $result ? Ets_faq::$trans['data_saved'] : Ets_faq::$trans['unkown_error'],
            )));
        }

        /*@todo search ajax product*/
        if (Tools::getValue('q')) {
            $keyword = Tools::getValue('q', false);
            if (!$keyword OR $keyword == '' OR Tools::strlen($keyword) < 1)
                die();
            $context = Ets_faq::getInstanceContext();
            if ($pos = strpos($keyword, ' (ref:')) {
                $keyword = Tools::substr($keyword, 0, $pos);
            }
            $excludeIds = Tools::getValue('excludeIds', false);
            if ($excludeIds && $excludeIds != 'NaN') {
                $excludeIds = explode(',', $excludeIds);
            } else {
                $excludeIds = array();
            }
            $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', true);
            $exclude_packs = (bool)Tools::getValue('exclude_packs', true);
            $imageType = self::getFormattedName('cart');

            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`
            		FROM `' . _DB_PREFIX_ . 'product` p
            		' . Shop::addSqlAssociation('product', 'p') . '
            		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)$context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover = 1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
            		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$context->language->id . ')
            		WHERE (pl.name LIKE \'%' . pSQL($keyword) . '%\' OR p.reference LIKE \'%' . pSQL($keyword) . '%\')' .
                ($excludeIds ? 'AND p.id_product NOT IN (' . implode(',', array_map('intval', $this->strToIds($excludeIds))) . ')' : '') .
                ($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                ($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                ' GROUP BY p.id_product';
            //die($sql);
            $items = Db::getInstance()->executeS($sql);
            if (ob_get_level() && ob_get_length() > 0) {
                ob_end_clean();
            }
            if ($items)
                foreach ($items AS $item) {
                    $image_link = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], (int)$item['id_image'], $imageType));
                    echo trim($item['name']) . '|' . (int)($item['id_product']) . '|' . $image_link . '|' . $item['reference'] . "\n";
                }
            die;
        }
    }

    public function displayAlerts($time)
    {
        $this->smarty->assign(array(
            'alerts' => $this->alerts,
            'time' => $time,
        ));
        return $this->display(__FILE__, 'admin-alerts.tpl');
    }

    public function getQuestions($active = false, $id_faq_group = false, $id_faq_question = false)
    {
        $questions = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("
            SELECT fq.id_faq_question, fq.id_faq_group, fql.id_lang, fql.question, fql.answer, fq.enabled, fq.product_ids 
            FROM " . _DB_PREFIX_ . "ets_faq_question fq
            LEFT JOIN " . _DB_PREFIX_ . "ets_faq_question_lang fql ON (fq.id_faq_question = fql.id_faq_question AND fql.id_lang = " . (int)$this->context->language->id . ") 
            WHERE 1 " . ($active ? " AND fq.enabled = " . (int)$active : "") . ($id_faq_group ? " AND id_faq_group=" . (int)$id_faq_group : "") . ($id_faq_question ? " AND fq.id_faq_question=" . (int)$id_faq_question : "") . "
            GROUP BY fq.id_faq_question
            ORDER BY fq.sort_order
        ");
        if (is_array($questions) && $questions)
            foreach ($questions as &$question) {
                $question['products'] = $this->getProducts($question['product_ids']);
            }
        if ($id_faq_question)
            return $questions[0];
        return $questions;
    }
    public static function getFormattedName($name)
    {
        $themeName = Ets_faq::getInstanceContext()->shop->theme_name;
        $nameWithoutThemeName = str_replace(['_' . $themeName, $themeName . '_'], '', $name);

        //check if the theme name is already in $name if yes only return $name
        if ($themeName !== null && strstr($name, $themeName) && ImageType::getByNameNType($name)) {
            return $name;
        }

        if (ImageType::getByNameNType($nameWithoutThemeName . '_' . $themeName)) {
            return $nameWithoutThemeName . '_' . $themeName;
        }

        if (ImageType::getByNameNType($themeName . '_' . $nameWithoutThemeName)) {
            return $themeName . '_' . $nameWithoutThemeName;
        }

        return $nameWithoutThemeName . '_default';
    }
    public function getProducts($ids)
    {
        if (!$ids)
            return array();
        $context = Ets_faq::getInstanceContext();
        $imageType =  self::getFormattedName('cart');
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT p.id_product, pl.`link_rewrite`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`reference`
            FROM `' . _DB_PREFIX_ . 'product` p ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)$context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)$context->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$context->language->id . ')
            WHERE 1 ' . (' AND p.id_product IN (' . pSQL(implode(',', array_map('pSQL', $this->strToIds($ids)))) . ')') . ' 
            GROUP BY p.id_product
            ORDER BY p.id_product');
        if (is_array($products) && $products) {
            foreach ($products as &$product) {
                $image_link = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($product['link_rewrite'], (int)$product['id_image'], $imageType));
                $product['image_link'] = $image_link;
            }
            return $products;
        }
        return array();
    }

    public function strToIds($str)
    {
        $ids = array();
        if ($str) {
            $arg = explode(',', $str);
            foreach ($arg as $id) {
                if ($id && !in_array((int)$id, $ids)) {
                    $ids[] = (int)$id;
                }
            }
        }
        return $ids;
    }

    public function hookDisplayFAQQuestion($params)
    {
        $question = isset($params['question']) && $params['question'] ? $params['question'] : array();
        $this->smarty->assign(array(
            'question' => $question,
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-question.tpl');
    }

    public function hookDisplayFAQGroupTab($params)
    {
        $group = isset($params['group']) && $params['group'] ? $params['group'] : array();
        $this->smarty->assign(array(
            'group' => $this->getGroupFaqs((int)$group['id_faq_group']),
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-tab.tpl');
    }

    public function getGroupFaqs($id_faq_group = false)
    {
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT fg.id_faq_group, fgl.group_name 
            FROM ' . _DB_PREFIX_ . 'ets_faq_group fg 
            LEFT JOIN  ' . _DB_PREFIX_ . 'ets_faq_group_lang fgl ON (fg.id_faq_group = fgl.id_faq_group) 
            WHERE fgl.id_lang = ' . (int)$this->context->language->id . ' AND fgl.id_shop = ' . (int)$this->context->shop->id . '
            ' . ($id_faq_group ? ' AND fg.id_faq_group = ' . (int)$id_faq_group : '') . '
            GROUP BY fg.id_faq_group
            ORDER BY fg.sort_order
        ');
        if ($id_faq_group)
            return $groups[0];
        return $groups;
    }

    public function hookDisplayFAQGroupList($params)
    {
        $group = isset($params['group']) && $params['group'] ? $params['group'] : array();
        $this->smarty->assign(array(
            'group' => $group,
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-list.tpl');
    }

    public function getFaqs($active = false, $id_faq_group = false)
    {
        $faqs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT fg.id_faq_group, fgl.group_name
            FROM ' . _DB_PREFIX_ . 'ets_faq_group fg
            LEFT JOIN  ' . _DB_PREFIX_ . 'ets_faq_group_lang fgl ON (fg.id_faq_group = fgl.id_faq_group AND fgl.id_lang = ' . (int)$this->context->language->id . ')
            WHERE fgl.id_shop = ' . (int)$this->context->shop->id . ($id_faq_group ? ' AND fg.id_faq_group = ' . (int)$id_faq_group : '') . '
            GROUP BY fg.id_faq_group
            ORDER BY fg.sort_order
        ');
        if (is_array($faqs) && $faqs)
            foreach ($faqs as &$faq)
                $faq['questions'] = $this->getQuestions($active, (int)$faq['id_faq_group']);
        if ($id_faq_group)
            return $faqs[0];
        return $faqs;
    }

    public function getFaqConfigs($forJs = false)
    {
        $configs = array();
        foreach (self::$configs['configs'] as $key => $val) {
            if ($forJs)
                $configKey = 'data-' . Tools::strtolower(str_replace('_', '-', str_replace('ETS_FAQ_', '', $key)));
            else
                $configKey = $key;
            $configs[$configKey] = Configuration::get($key, isset($val['lang']) && $val['lang'] ? $this->context->language->id : null);
        }
        return $configs;
    }

    public function requestForm()
    {
        if (Tools::isSubmit('request_form') && ($mmObj = Tools::getValue('faq_object')) && in_array($mmObj, array('FAQ_Group', 'FAQ_Question'))) {
            $obj = ($itemId = (int)Tools::getValue('itemId')) && $itemId > 0 ? new $mmObj($itemId) : new $mmObj();
            die(json_encode(array(
                'form' => $obj->renderForm(),
                'itemId' => $itemId,
            )));
        }
    }

    public function displayAdminJs()
    {
        $this->smarty->assign(array(
            'js_dir_path' => $this->_path . 'views/js/',
        ));
        return $this->display(__FILE__, 'admin-js.tpl');
    }

    public function renderForm()
    {
        $group = new FAQ_Group();
        $config = new FAQ_Config();
        $this->smarty->assign(array(
            'groupForm' => $group->renderForm(),
            'configForm' => $config->renderForm(),
            'url_base_img' => $this->_path . 'views/img/upload/',
            'faqBaseAdminUrl' => $this->baseAdminUrl(),
            'id_lang' => $this->context->language->id,
            'faq_configs' => $this->getFaqConfigs(),
            'faq_msg_delete' => $this->l('Are you sure you want to delete this item?'),
        ));
        return $this->display(__FILE__, 'admin-form.tpl');
    }

    public function baseAdminUrl()
    {
        return $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name;
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/faq-front.css');
        if ($this->is17) {
            $this->context->controller->addCSS($this->_path . 'views/css/fix17.css');
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/fix16.css');
        }
        $this->context->controller->addJS($this->_path . 'views/js/faq-front.js');
        $this->smarty->assign(array(
            '_VER_17' => $this->is17,
            'static_token' => Tools::getToken(false),
            'faq_config' => $this->getFaqConfigs(),
            'url_ajax' => $this->context->link->getModuleLink($this->name, 'faqs', array(), true)
        ));
        if(($page = Tools::strtolower(trim(Tools::getValue('controller')))) && in_array($page, array('product', 'faqs')) && $this->checkUserCaptcha() )
        {
            if (($captcha_type = Configuration::get('ETS_FAQ_CAPTCHA_TYPE')) == 'google' || $captcha_type == 'google_v3') {
                $this->smarty->assign(array(
                    'ETS_FAQ_CAPTCHA_TYPE' => $captcha_type,
                    'ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY' => Configuration::get('ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY'),
                    'ETS_FAQ_GOOGLE_CAPTCHA_THEME' => 'light',
                    'ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY3' => Configuration::get('ETS_FAQ_GOOGLE_CAPTCHA_SITE_KEY3'),
                    'ETS_FAQ_GOOGLE_V3_POSITION' => 'bottomright',
                    'hl' => $this->context->language->iso_code
                ));
                return $this->display(__FILE__, 'head.tpl');
            }
        }
        
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (trim(Tools::getValue('controller')) == 'AdminModules' && trim(Tools::getValue('configure')) == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/faq-backend.css');
            $this->context->controller->addJqueryUi('ui.sortable');
            if ($this->is17) {
                $this->context->controller->addJqueryUi('ui.widget');
            }
            $this->context->controller->addJqueryPlugin('tagify');
        }
    }

    public function addCss17($cssFile, $id = false, $local = true)
    {
        $this->context->controller->registerStylesheet($id ? $id : '', $cssFile, array('media' => 'all', 'priority' => 150, 'server' => $local ? 'local' : 'remote'));
    }

    public function modulePath()
    {
        return $this->_path;
    }

    public function getMetaTag($id_lang)
    {
        $ret = array();
        $ret['meta_title'] = Configuration::get('ETS_FAQ_META_TITLE', $id_lang) ? Configuration::get('ETS_FAQ_META_TITLE', $id_lang) : Configuration::get('PS_SHOP_NAME');
        $ret['meta_description'] = Configuration::get('ETS_FAQ_META_DESCRIPTION', $id_lang) ? Configuration::get('ETS_FAQ_META_DESCRIPTION', $id_lang) : '';
        $ret['meta_keywords'] = Configuration::get('ETS_FAQ_META_KEYWORDS', $id_lang) ? Configuration::get('ETS_FAQ_META_KEYWORDS', $id_lang) : '';
        return $ret;
    }

    public function getProductByIdQuestion($id_faq_question = false)
    {
        if (!$id_faq_question)
            return array();
        $question = Db::getInstance()->getRow('
            SELECT *
            FROM ' . _DB_PREFIX_ . 'ets_faq_question fq
            WHERE ' . (' fq.id_faq_question=' . (int)$id_faq_question) . '
        ');
        if ($question && (int)$question['display_on_product'] == 3)
            return $this->getProducts($question['product_ids']);
        return array();
    }

    public function getModulePath()
    {
        return $this->_path;
    }

    public function hookDisplayFAQGroupTabs($params)
    {
        $this->smarty->assign(array(
            'groups' => $this->getGroupFaqs(),
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-tabs.tpl');
    }

    public function hookDisplayFAQGroupLists($params)
    {
        $getfaqs = $this->getFaqs();
        $this->smarty->assign(array(
            'groups' => $getfaqs,
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-lists.tpl');
    }

    public function hookDisplayFAQQuestionGroup($params)
    {
        $id_faq_group = isset($params['id_faq_group']) && $params['id_faq_group'] ? $params['id_faq_group'] : false;
        $questions = isset($params['questions']) && $params['questions'] ? $params['questions'] : array();
        $this->smarty->assign(array(
            'questions' => $questions,
            'id_faq_group' => $id_faq_group,
            'is17' => $this->is17
        ));
        return $this->display(__FILE__, 'admin-group-questions.tpl');
    }

    public function hookDisplayFAQConfigs()
    {
        $configStr = '';
        if ($configs = $this->getFaqConfigs()) {
            foreach ($configs as $key => $val)
                $configStr .= 'data-' . Tools::strtolower(str_replace('_', '-', str_replace('ETS_FAQ_', '', $key))) . '="' . Tools::strtolower($val) . '" ';
        }
        return $configStr;
    }

    public function hookModuleRoutes($params)
    {
        $page_rewrite = Configuration::get('ETS_FAQ_REWRITE_URL', $this->context->language->id);
        if (!$page_rewrite)
            return array();
        $routes = array(
            'faqspage' => array(
                'controller' => 'faqs',
                'rule' => $page_rewrite,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_faq',
                ),
            ),
        );
        return $routes;
    }

    public function hookDisplayFAQAskAQuestionForm($params)
    {
        $customer_info = array();
        if ($this->context->customer->isLogged() && isset($this->context->customer->id)) {
            $customer = new Customer($this->context->customer->id);
            $customer_info['name'] = $customer->firstname . ' ' . $customer->lastname;
            $customer_info['email'] = $customer->email;
            $id_address = Address::getFirstCustomerAddressId($customer->id, true);
            $address = new Address($id_address);
            if ($address->phone) {
                $customer_info['phone'] = $address->phone;
            } elseif ($address->phone_mobile) {
                $customer_info['phone'] = $address->phone_mobile;
            }
        }
        $layout = isset($params['layout']) && $params['layout'] ? $params['layout'] : '';
        $assign = array(
            'configs' => $this->getFaqConfigs(),
            'action' => $this->context->link->getModuleLink('ets_faq', 'faqs', array('send_mail' => '1'), true),
            'layout' => $layout,
            'img_base_dir' => $this->_path . 'views/img/',
            'id_product' => Tools::getValue('controller')=='product' ? Tools::getValue('id_product'):0,
            'customer_info' => $customer_info,
        );
        $this->context->smarty->assign($assign);
        return $this->display(__FILE__, 'front-ask-aquestion-form.tpl');
    }

    public function hookDisplayProductTabContent($params)
    {
        if (!$this->is17 && Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE') == 'displayProductTabContent') {
            return $this->_hookModule($params);
        }
    }

    public function _hookModule($params)
    {
        $id_product = isset($params['product']) && is_object($params['product']) ? (isset($params['product']->id) && $params['product']->id ? $params['product']->id : false) : (isset($params['product']['id']) && $params['product']['id'] ? $params['product']['id'] : Tools::getValue('id_product', false));
        if (!$id_product)
            return false;
        $assign = array(
            'configs' => $this->getFaqConfigs(),
            'faqs' => $this->getFaqsInProduct($id_product),
            'action' => $this->context->link->getModuleLink('ets_faq', 'faqs', array('send_mail' => '1'), true),
            'is17' => $this->is17
        );
        $this->context->smarty->assign($assign);
        return $this->display(__FILE__, 'front-product-faqs.tpl');
    }

    public function getFaqsInProduct($id_product)
    {
        if (!$id_product)
            return array();
        $faqs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("
            SELECT fq.id_faq_question, fq.id_faq_group, fql.question, fql.answer, fq.enabled 
            FROM `" . _DB_PREFIX_ . "ets_faq_question` fq 
            LEFT JOIN `" . _DB_PREFIX_ . "ets_faq_question_lang` fql ON (fq.id_faq_question = fql.id_faq_question AND fql.id_lang = " . (int)$this->context->language->id . ")
            WHERE fq.enabled = 1 AND ((fq.display_on_product = 2) OR (fq.display_on_product = 3 AND FIND_IN_SET(" . (int)$id_product . ", fq.product_ids)))
            GROUP BY fq.id_faq_question
            ORDER BY fq.id_faq_question
        ");
        if ($faqs)
            return $faqs;
        return array();
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        if ($this->is17 && Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE') == 'displayAfterProductThumbs' && Tools::getValue('action')!='quickview') {
            return $this->_hookModule($params);
        }
    }

    public function hookDisplayFAQOnPageProduct($params)
    {
        if ($this->is17 && Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE') == 'displayFAQOnPageProduct') {
            return $this->_hookModule($params);
        }
    }

    public function hookDisplayFooterProduct($params)
    {
        if (Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE') == 'displayFooterProduct') {
            return $this->_hookModule($params);
        }
    }

    // Product left column
    public function hookDisplayProductAdditionalInfo($params)
    {
        if (($this->is17 && Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE') == 'displayProductAdditionalInfo') || Tools::getValue('action')=='quickview' ) {
            return $this->_hookModule($params);
        }
    }

    public function hookDisplayFAQCaptcha()
    {
        $rand = md5((string)rand());
        if (($page = Tools::strtolower(trim(Tools::getValue('controller')))) && in_array($page, array('product', 'faqs')))
            return $this->renderCaptcha($rand);
    }
    public function checkUserCaptcha()
    {
        if(Configuration::get('ETS_FAQ_ENABLE_CAPTCHA_ASK_QUESTION_FROM') && !Configuration::get('ETS_FAQ_NOT_REQUIRE_REGISTERED') || !$this->context->customer->logged)
        {
            return true;
        }
    }
    public function renderCaptcha($rand)
    {
        
        if($this->checkUserCaptcha())
        {
            $ETS_FAQ_CAPTCHA_TYPE = Configuration::get('ETS_FAQ_CAPTCHA_TYPE');
            if($ETS_FAQ_CAPTCHA_TYPE=='image')
            {
                $this->smarty->assign(array(
                    'captcha_image' => $this->context->link->getModuleLink('ets_faq', 'captcha', array('rand' => $rand)),
                    'rand' => $rand,
                    'modules_dir' => _MODULE_DIR_,
                ));
                return $this->display(__FILE__, 'front-faq-captcha.tpl');
            }
            elseif($ETS_FAQ_CAPTCHA_TYPE=='google')
            {
                return $this->display(__FILE__,'captcha_google.tpl');
            }
            elseif($ETS_FAQ_CAPTCHA_TYPE=='google_v3')
            {
                return $this->display(__FILE__,'captcha_google_v3.tpl');
            }
        }
        
    }

    public function sendMessage($vardata)
    {
        $configs = $this->getFaqConfigs();
        if (is_array($vardata) && $vardata) {
            if(isset($vardata['id_product']) && $vardata['id_product'])
            {
                $product = new Product($vardata['id_product'],false,$this->context->language->id);
                $link_product = $this->context->link->getProductLink($product);
                $product_name = $this->displayText($product->name,'a',null,null,$link_product);
            }
            else
                $product_name='';
            $data = array(
                '{faq_name}' => $vardata['faq_name'],
                '{faq_phone}' =>  $vardata['faq_phone'] ? $this->displayText(sprintf($this->l('Telephone: %s'),$vardata['faq_phone']),'p'):'',
                '{faq_phone_txt}' => $vardata['faq_phone'] ? sprintf($this->l('Telephone: %s'),$vardata['faq_phone']):'',
                '{faq_email}' => $vardata['faq_email'],
                '{product_name}' => isset($vardata['id_product']) && $vardata['id_product'] ? $this->displayText($this->l('Related product:').' '.$product_name,'p'):'',
                '{product_name_txt}' =>isset($vardata['id_product']) && $vardata['id_product'] ? sprintf($this->l('Related product: %s'),$product_name):'',
                '{faq_your_question}' => $vardata['faq_your_question'],
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            );
            if (Validate::isEmail($configs['ETS_FAQ_SEND_QUESTION_TO_EMAIL']) && Mail::Send(
                    (int)$this->context->language->id,
                    'ask_a_question',
                    Mail::l('A new question has just been submitted', (int)$this->context->language->id),
                    $data,
                    $configs['ETS_FAQ_SEND_QUESTION_TO_EMAIL'],
                    null,
                    $vardata['faq_email'],
                    $vardata['faq_name'],
                    null,
                    null, dirname(__FILE__) . '/mails/', false, (int)$this->context->shop->id)
            )
                return true;
            else
                return false;
        }
        return false;
    }

    public function getBreadCrumb()
    {
        $title_page = Configuration::get('ETS_FAQ_META_TITLE', $this->context->language->id);
        $nodes = array();
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $title_page ? $title_page : $this->l('FAQs'),
            'url' => $this->getLink('faqs')
        );
        if ($this->is17)
            return array('links' => $nodes, 'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }

    public function getLink($controller = 'faqs', $params = array())
    {
        $context = Ets_faq::getInstanceContext();
        $page_rewrite = Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) ? Configuration::get('ETS_FAQ_REWRITE_URL', $context->language->id) : $this->l('faqs');
        $lbLink = new FAQ_Link();
        if ($page_rewrite && Configuration::get('PS_REWRITING_SETTINGS')) {
            return $lbLink->getBaseLinkFriendly($context->shop->id, true) . $lbLink->getLangLinkFriendly($context->language->id, $context, $context->shop->id) . $page_rewrite;
        }
        return $context->link->getModuleLink('ets_faq', $controller, $params);
    }

    public function displayBreadcrumb($nodes)
    {
        $this->context->smarty->assign(array('nodes' => $nodes));
        return $this->display(__FILE__, 'front-nodes.tpl');
    }

    public function displayText($content=null,$tag='',$class=null,$id=null,$href=null,$blank=false,$src = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $this->smarty->assign(
            array(
                'tag_content' =>$content,
                'tag_name' => $tag,
                'tag_class'=> $class,
                'tag_id' => $id,
                'href' => $href,
                'blank' => $blank,
                'src' => $src,
                'attr_name' => $name,
                'value' => $value,
                'type' => $type,
                'data_id_product' => $data_id_product,
                'attr_datas' => $attr_datas,
                'rel' => $rel,
            )
        );
        return $this->display(__FILE__,'html.tpl');
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Context
     */
    public static function getInstanceContext()
    {
        /** @var Ets_faq $module */
        $module = Module::getInstanceByName('ets_faq');
        return $module->getContext();
    }
}