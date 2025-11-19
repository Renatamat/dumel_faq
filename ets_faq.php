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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Ets_faq extends Module implements WidgetInterface
{
    public static $groups;
    public static $questions;
    public static $trans;
    public static $configs;
    public static $position_hook = array();
    public $alerts;
    public $baseAdminPath;
    /**
     * Kept for backwards compatibility with templates/controllers that still
     * expect version flags. PS 8.2 is our only target so this always evaluates
     * to true and simply avoids undefined property notices.
     *
     * @var bool
     */
    public $is17 = true;
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
        $this->description = $this->l('Create frequently asked questions (FAQ) page and product tab for PrestaShop 8.2.');
        $this->refs = 'https://prestahero.com/';
        $this->module_key = 'a07b2a104f0823a1cb3dd05cd4d8b9fc';
        $this->ps_versions_compliancy = array('min' => '8.2.0.0', 'max' => '8.2.99.99');
        $this->translates();
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
            && $this->registerHook('displayFAQConfigs')
            && $this->registerHook('displayFAQOnPageProduct')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayFaqAnywhere')
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
        ));
        return $this->display(__FILE__, 'admin-group-question.tpl');
    }

    public function hookDisplayFAQGroupTab($params)
    {
        $group = isset($params['group']) && $params['group'] ? $params['group'] : array();
        $this->smarty->assign(array(
            'group' => $this->getGroupFaqs((int)$group['id_faq_group']),
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

    public function getFaqTemplateVariables(): array
    {
        return array(
            'configs' => $this->getFaqConfigs(),
            'faqs' => $this->getFaqs(true),
        );
    }

    protected function renderFaqContent(array $templateVars): string
    {
        $this->context->smarty->assign($templateVars);

        return $this->fetch('module:ets_faq/views/templates/front/_partials/faq-content.tpl');
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
        $this->context->controller->addCSS($this->_path . 'views/css/faq-theme.css');
        $this->context->controller->addJS($this->_path . 'views/js/faq-front.js');
    }

    public function hookDisplayFaqAnywhere(array $params)
    {
        $templateVars = $this->getFaqTemplateVariables();

        if (empty($templateVars['faqs'])) {
            return '';
        }

        return $this->renderFaqContent($templateVars);
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (trim(Tools::getValue('controller')) == 'AdminModules' && trim(Tools::getValue('configure')) == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/faq-backend.css');
            $this->context->controller->addJqueryUi('ui.sortable');
            $this->context->controller->addJqueryUi('ui.widget');
            $this->context->controller->addJqueryPlugin('tagify');
        }
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
        ));
        return $this->display(__FILE__, 'admin-group-tabs.tpl');
    }

    public function hookDisplayFAQGroupLists($params)
    {
        $getfaqs = $this->getFaqs();
        $this->smarty->assign(array(
            'groups' => $getfaqs,
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

    public function renderFaqWidget(array $configuration = array())
    {
        $hookName = isset($configuration['hook']) ? $configuration['hook'] : null;
        if (!$this->shouldDisplayOnHook($hookName)) {
            return '';
        }
        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (empty($variables['faqs'])) {
            return '';
        }
        $this->context->smarty->assign($variables);
        return $this->display(__FILE__, 'front-product-faqs.tpl');
    }

    public function renderWidget($hookName, array $configuration)
    {
        $configuration['hook'] = $hookName;
        return $this->renderFaqWidget($configuration);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $idProduct = $this->resolveProductId($configuration);
        if (!$idProduct) {
            return array();
        }
        $faqs = $this->getFaqsInProduct($idProduct);
        if (!$faqs) {
            return array();
        }
        return array(
            'configs' => $this->getFaqConfigs(),
            'faqs' => $faqs,
        );
    }

    protected function resolveProductId(array $configuration)
    {
        if (isset($configuration['product'])) {
            if (is_object($configuration['product']) && isset($configuration['product']->id)) {
                return (int)$configuration['product']->id;
            }
            if (is_array($configuration['product']) && isset($configuration['product']['id'])) {
                return (int)$configuration['product']['id'];
            }
        }
        if (isset($configuration['id_product'])) {
            return (int)$configuration['id_product'];
        }
        return (int)Tools::getValue('id_product', 0);
    }

    protected function shouldDisplayOnHook($hookName = null)
    {
        if (!Configuration::get('ETS_FAQ_ENABLE_FREQUENTLY_ON_PRODUCT')) {
            return false;
        }
        if ($hookName === null) {
            return true;
        }
        if ($hookName === 'displayFAQOnPageProduct') {
            return true;
        }
        if ($hookName === 'displayAfterProductThumbs' && Tools::getValue('action') === 'quickview') {
            return false;
        }
        if ($hookName === 'displayProductAdditionalInfo' && Tools::getValue('action') === 'quickview') {
            return true;
        }
        $position = Configuration::get('ETS_FAQ_POSITION_ON_PRODUCT_PAGE');
        return $position === $hookName;
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
        $params['hook'] = 'displayAfterProductThumbs';
        return $this->renderFaqWidget($params);
    }

    public function hookDisplayFAQOnPageProduct($params)
    {
        $params['hook'] = 'displayFAQOnPageProduct';
        return $this->renderFaqWidget($params);
    }

    public function hookDisplayFooterProduct($params)
    {
        $params['hook'] = 'displayFooterProduct';
        return $this->renderFaqWidget($params);
    }

    // Product left column
    public function hookDisplayProductAdditionalInfo($params)
    {
        $params['hook'] = 'displayProductAdditionalInfo';
        return $this->renderFaqWidget($params);
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
        return array('links' => $nodes, 'count' => count($nodes));
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