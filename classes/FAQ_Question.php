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

class FAQ_Question extends FAQ_Obj
{
    public $id_faq_question;
    public $id_faq_group;
    public $question;
    public $answer;
    public $display_on_product=2;
    public $product_ids;
    public $enabled;
    public $sort_order;
    public static $definition = array(
        'table' => 'ets_faq_question',
        'primary' => 'id_faq_question',
        'multilang' => true,
        'fields' => array(
            'id_faq_group' => array('type' => self::TYPE_INT, 'validate' =>'isUnsignedId'),
            'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'display_on_product' => array('type' => self::TYPE_INT, 'validate'=>'isUnsignedInt'),
            'product_ids' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'sort_order' => array('type'=>self::TYPE_INT, 'validate'=>'isUnsignedInt'),
            // Lang fields
            'question' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'answer' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        )
    );

    public function __construct($id_item = null, $id_lang = null, Context $context = null)
    {
        parent::__construct($id_item, $id_lang);
        unset($context);
        $this->setFields(Ets_faq::$questions);
    }
}
