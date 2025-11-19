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

class FAQ_Group extends FAQ_Obj
{
    public $group_name;
    public $sort_order;
    public $date_add;
    public static $definition = array(
        'table' => 'ets_faq_group',
        'primary' => 'id_faq_group',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'sort_order' => array('type' => self::TYPE_INT, 'valiate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            // Lang fields
            'group_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
        )
    );

    public function __construct($id_item = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            foreach (self::$definition['fields'] as $field => $params) {
                $temp = $this->$field;
                if (isset($params['lang']) && $params['lang'] && !isset($temp[$lang['id_lang']])) {
                    $temp[$lang['id_lang']] = '';
                }
                $this->$field = $temp;
            }
        }
        unset($context);
        $this->setFields(Ets_faq::$groups);
    }
}
