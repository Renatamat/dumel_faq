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

class FAQ_Obj extends ObjectModel
{
    public $fields;
    public $sort_order;
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = new Ets_faq();
        $configs = $this->fields['configs'];
        $fields_form = array();
        $fields_form['form'] = $this->fields['form'];
        if ($configs) {
            foreach ($configs as $key => $config) {
                if (isset($config['type']) && in_array($config['type'], array('sort_order')))
                    continue;
                $confFields = array(
                    'name' => $key,
                    'type' => $config['type'],
                    'label' => $config['label'],
                    'class' => isset($config['class']) ? $config['class'] : false,
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] ? true : false,
                    'showRequired' => isset($config['showRequired']) && $config['showRequired'] ? true : false,
                    
                    'maxchar' => isset($config['maxchar']) && $config['maxchar'] ? true : false,
                    'rows' => isset($config['rows']) && $config['rows'] ? $config['rows'] : false,
                    'cols' => isset($config['cols']) && $config['cols'] ? $config['cols'] : false,
                    'autoload_rte' => isset($config['autoload_rte']) && $config['autoload_rte'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                    'prefix' => isset($config['prefix']) && $config['prefix'] ? $config['prefix'] : false,
                    'values' => isset($config['values']) ? $config['values'] : false,
                    'lang' => isset($config['lang']) ? $config['lang'] : false,
                    'col' => isset($config['col']) && $config['col'] ? $config['col'] : false,
                    'hint' => isset($config['hint']) && $config['hint'] ? $config['hint'] : false,
                    'placeholder' => isset($config['placeholder']) && $config['placeholder'] ? $config['placeholder'] : false,
                    'autocomplete' => isset($config['autocomplete']) && $config['autocomplete'] ? $config['autocomplete'] : false,
                    'validate' => isset($config['validate']) && $config['validate'] ? $config['validate'] : false,
                    'hide_delete' => isset($config['hide_delete']) ? $config['hide_delete'] : false,
                    'display_img' => $this->id && isset($config['type']) && $config['type'] == 'file' && $this->$key != '' && @file_exists(dirname(__FILE__) . '/../views/img/upload/' . $this->$key) ? $helper->module->modulePath() . 'views/img/upload/' . $this->$key : false,
                    'img_del_link' => $this->id && isset($config['type']) && $config['type'] == 'file' && $this->$key != '' && @file_exists(dirname(__FILE__) . '/../views/img/upload/' . $this->$key) ? $helper->module->baseAdminUrl() . '&deleteimage=' . $key . '&itemId=' . $this->id . '&faq_object=FAQ_' . Tools::ucfirst($fields_form['form']['name']) : false,
                    'base_admin_url' => isset($config['base_admin_url']) ? $config['base_admin_url'] : false,
                );

                if (isset($config['tree']) && $config['tree']) {
                    $confFields['tree'] = $config['tree'];
                    if (isset($config['tree']['use_checkbox']) && $config['tree']['use_checkbox'])
                        $confFields['tree']['selected_categories'] = explode(',', $this->$key);
                    else
                        $confFields['tree']['selected_categories'] = array($this->$key);
                }
                foreach ($confFields as $keyfield => $field) {
                    if ($field === false)
                        unset($confFields[$keyfield]);
                }
                unset($field);
                $fields_form['form']['input'][] = $confFields;
            }
        }
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'save_' . $this->fields['form']['name'];
        $link = new Link();
        $helper->currentIndex = $link->getAdminLink('AdminModules', true) . '&configure=ets_faq';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';
        if ($configs) {
            foreach ($configs as $key => $config) {

                if ($config['type'] == 'checkbox')
                    $fields[$key] = $this->id ? explode(',', $this->$key) : (isset($config['default']) && $config['default'] ? $config['default'] : array());
                elseif (isset($config['lang']) && $config['lang']) {
                    foreach ($languages as $l) {
                        $temp = $this->$key;
                        $fields[$key][$l['id_lang']] = $this->id ? $temp[$l['id_lang']] : (isset($config['default']) ? $config['default'] : null);
                    }
                } elseif ($config['type'] == 'search') {
                    $fields[$key] = $this->id ? $helper->module->getProductByIdQuestion($this->id) : null;
                } elseif (!isset($config['tree']))
                    $fields[$key] = $this->id ? $this->$key : (isset($config['default']) ? $config['default'] : null);
            }
        }

        $helper->tpl_vars = array(
            'base_url' => Ets_faq::getInstanceContext()->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => Ets_faq::getInstanceContext()->controller->getLanguages(),
            'id_language' => Ets_faq::getInstanceContext()->language->id,
            'key_name' => 'id_faq_' . $fields_form['form']['name'],
            'item_id' => $this->id,
            'faq_object' => 'FAQ_' . Tools::ucfirst($fields_form['form']['name']),
            'list_item' => true,
            'image_baseurl' => $helper->module->modulePath() . 'views/img/',
        );
        return str_replace(array('id="ets_faq_menu_form"', 'id="fieldset_0"'), '', $helper->generateForm(array($fields_form)));
    }

    public function getFieldVals()
    {
        if (!$this->id)
            return array();
        $vals = array();
        foreach ($this->fields['configs'] as $key => $config) {
            if (property_exists($this, $key))
                $vals[$key] = $this->$key;
        }
        $vals['id_faq_' . $this->fields['form']['name']] = (int)$this->id;
        unset($config);
        return $vals;
    }

    public function clearImage($image)
    {
        $configs = $this->fields['configs'];
        $errors = array();
        $success = array();
        if (!$this->id)
            $errors[] = Ets_faq::$trans['object_empty'];
        elseif (!isset($configs[$image]['type']) || isset($configs[$image]['type']) && $configs[$image]['type'] != 'file')
            $errors[] = Ets_faq::$trans['field_not_valid'];
        elseif (isset($configs[$image]) && !isset($configs[$image]['required']) || (isset($configs[$image]['required']) && !$configs[$image]['required'])) {
            $imageName = $this->$image;
            $imagePath = dirname(__FILE__) . '/../views/img/upload/' . $imageName;
            if ($imageName && file_exists($imagePath)) {
                @unlink($imagePath);
                $this->$image = '';
                if ($this->update()) {
                    $success[] = Ets_faq::$trans['image_deleted'];
                } else
                    $errors[] = Ets_faq::$trans['unkown_error'];
            }
        } else
            $errors[] = $configs[$image]['label'] . Ets_faq::$trans['required_text'];
        return array('errors' => $errors, 'success' => $success);
    }

    public function deleteObj()
    {
        $errors = array();
        $success = array();
        $configs = $this->fields['configs'];
        $images = array();
        foreach ($configs as $key => $config) {
            if ($config['type'] == 'file' && $this->$key && @file_exists(dirname(__FILE__) . '/../views/img/upload/' . $this->$key))
                $images[] = dirname(__FILE__) . '/../views/img/upload/' . $this->$key;
        }
        if (!$this->delete())
            $errors[] = Ets_faq::$trans['cannot_delete'];
        else {
            foreach ($images as $image)
            {
                if(file_exists($image))
                    @unlink($image);
            }
            $success[] = Ets_faq::$trans['item_deleted'];
            if (isset($configs['sort_order']) && $configs['sort_order']) {
                Db::getInstance()->execute("
                    UPDATE " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['name']) . "
                    SET sort_order = sort_order - 1 
                    WHERE sort_order>" . (int)$this->sort_order . " " . (isset($configs['sort_order']['order_group']) && ($orderGroup = $configs['sort_order']['order_group']) ? " AND " . pSQL($orderGroup) . "=" . (int)$this->$orderGroup : "") . "
                ");
            }
            if ($this->id && isset($this->fields['form']['connect_to']) && $this->fields['form']['connect_to']
                && ($subs = Db::getInstance()->executeS("SELECT id_faq_" . pSQL($this->fields['form']['connect_to']) . " FROM " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['connect_to']) . " WHERE id_faq_" . pSQL($this->fields['form']['name']) . "=" . (int)$this->id))) {
                foreach ($subs as $sub) {
                    $className = 'FAQ_' . Tools::ucfirst(Tools::strtolower($this->fields['form']['connect_to']));
                    if (class_exists($className)) {
                        $obj = new $className((int)$sub['id_faq_' . $this->fields['form']['connect_to']]);
                        $obj->deleteObj();
                    }
                }
            }
        }
        return array('errors' => $errors, 'success' => $success);
    }

    public function maxVal($key, $group = false, $groupval = 0)
    {
        return ($max = Db::getInstance()->getValue("SELECT max(" . pSQL($key) . ") FROM " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['name']) . ($group && ($groupval > 0) ? " WHERE " . pSQL($group) . "=" . (int)$groupval : ''))) ? (int)$max : 0;
    }

    public function updateOrder($previousId = 0, $groupdId = 0)
    {
        $group = isset($this->fields['configs']['sort_order']['order_group']) && $this->fields['configs']['sort_order']['order_group'] ? $this->fields['configs']['sort_order']['order_group'] : false;
        if (!$groupdId && $group)
            $groupdId = $this->$group;
        $oldOrder = $this->sort_order;
        if ($group && $groupdId && property_exists($this, $group) && $this->$group != $groupdId) {
            Db::getInstance()->execute("
                    UPDATE " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['name']) . "
                    SET sort_order=sort_order-1 
                    WHERE sort_order>" . (int)$this->sort_order . " AND id_faq_" . pSQL($this->fields['form']['name']) . "!=" . (int)$this->id . "
                          " . (" AND " . pSQL($group) . "=" . (int)$this->$group));
            $this->$group = $groupdId;
            $changeGroup = true;
        } else
            $changeGroup = false;
        if ($previousId > 0) {
            $objName = 'FAQ_' . Tools::ucfirst($this->fields['form']['name']);
            $obj = new $objName($previousId);
            if ($obj->sort_order > 0)
                $this->sort_order = $obj->sort_order + 1;
            else
                $this->sort_order = 1;
        } else
            $this->sort_order = 1;
        if ($this->update()) {
            Db::getInstance()->execute("
                    UPDATE " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['name']) . "
                    SET sort_order=sort_order+1 
                    WHERE sort_order>=" . (int)$this->sort_order . " AND id_faq_" . pSQL($this->fields['form']['name']) . "!=" . (int)$this->id . "
                          " . ($group && $groupdId ? " AND " . pSQL($group) . "=" . (int)$this->$group : ""));

            if (!$changeGroup && $this->sort_order != $oldOrder) {
                $rs = Db::getInstance()->execute("
                        UPDATE " . _DB_PREFIX_ . "ets_faq_" . pSQL($this->fields['form']['name']) . "
                        SET sort_order=sort_order-1
                        WHERE sort_order>" . ($this->sort_order > $oldOrder ? (int)($oldOrder) : (int)($oldOrder + 1)) . ($group && $groupdId ? " AND " . pSQL($group) . "=" . (int)$this->$group : ""));
                return $rs;
            }
            return true;
        }
        return false;
    }

    public function saveData()
    {
        $errors = array();
        $success = array();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $configs = $this->fields['configs'];
        if ($configs) {
            foreach ($configs as $key => $config) {
                if ($config['type'] == 'sort_order')
                    continue;
                if (isset($config['lang']) && $config['lang']) {
                    if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key . '_' . $id_lang_default)) == '') {
                        $errors[] = $config['label'] . ' ' . Ets_faq::$trans['required_text'];
                    }
                } else {
                    if (isset($config['required']) && $config['required'] && isset($config['type']) && $config['type'] == 'file') {
                        if ($this->$key == '' && !isset($_FILES[$key]['size']))
                            $errors[] = $config['label'] . ' ' . Ets_faq::$trans['required_text'];
                        elseif (isset($_FILES[$key]['size'])) {
                            $fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
                            if ($fileSize > 1000)
                                $errors[] = $config['label'] . ' ' . Ets_faq::$trans['file_too_large'];
                        }
                    } else {
                        if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key)) == '') {
                            $errors[] = $config['label'] . ' ' . Ets_faq::$trans['required_text'];
                        } elseif (!is_array(Tools::getValue($key)) && isset($config['validate']) && method_exists('Validate', $config['validate'])) {
                            $validate = $config['validate'];
                            if (trim(Tools::getValue($key)) && !Validate::$validate(trim(Tools::getValue($key))))
                                $errors[] = $config['label'] . ' ' . Ets_faq::$trans['invalid_text'];
                            unset($validate);
                        } elseif (!Validate::isCleanHtml(trim(Tools::getValue($key)))) {
                            $errors[] = $config['label'] . ' ' . Ets_faq::$trans['required_text'];
                        }
                    }
                }
            }
        }
        if(($display_on_product = (int)Tools::getValue('display_on_product')) && $display_on_product==3)
        {
            $product_ids = Tools::getValue('product_ids');
            if(!$product_ids)
                $errors[] =  $configs['question_product']['label']. ' ' . Ets_faq::$trans['required_text'];
            elseif(!Validate::isCleanHtml($product_ids))
                $errors[] =  $configs['question_product']['label']. ' ' . Ets_faq::$trans['invalid_text'];
        }
        if (!$errors) {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['type']) && $config['type'] == 'sort_order') {
                        if (!$this->id) {
                            if (!isset($config['order_group']) || isset($config['order_group']) && !$config['order_group'])
                                $this->$key = $this->maxVal($key) + 1;
                            else {
                                $orderGroup = $config['order_group'];
                                $this->$key = $this->maxVal($key, $orderGroup, (int)$this->$orderGroup) + 1;
                            }
                        }
                    } elseif (isset($config['lang']) && $config['lang']) {
                        $valules = array();
                        foreach ($languages as $lang) {
                            if ($config['type'] == 'switch')
                                $valules[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                            else
                                $valules[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                        }
                        $this->$key = $valules;
                    } elseif ($config['type'] == 'switch') {
                        $this->$key = (int)Tools::getValue($key) ? 1 : 0;
                    } elseif ($config['type'] == 'file') {
                        //Upload file
                        if (isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                            $salt = Tools::substr(sha1(microtime()), 0, 10);
                            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                            $imageName = @file_exists(dirname(__FILE__) . '/../views/img/upload/' . Tools::strtolower($_FILES[$key]['name'])) ? $salt . '-' . Tools::strtolower($_FILES[$key]['name']) : Tools::strtolower($_FILES[$key]['name']);
                            $fileName = dirname(__FILE__) . '/../views/img/upload/' . $imageName;
                            if (file_exists($fileName)) {
                                $errors[] = $config['label'] . ' ' . Ets_faq::$trans['file_existed'];
                            } else {
                                $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                if (!$errors && isset($_FILES[$key]) &&
                                    !empty($_FILES[$key]['tmp_name']) &&
                                    !empty($imagesize) &&
                                    in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                                ) {
                                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                    if ($error = ImageManager::validateUpload($_FILES[$key]))
                                        $errors[] = $error;
                                    elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                        $errors[] = Ets_faq::$trans['can_not_upload'];
                                    elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                                        $errors[] = Ets_faq::$trans['upload_error_occurred'];
                                    if (file_exists($temp_name))
                                        @unlink($temp_name);
                                    if (!$errors) {
                                        if ($this->$key != '') {
                                            $oldImage = dirname(__FILE__) . '/../views/img/upload/' . $this->$key;
                                            if (file_exists($oldImage))
                                                @unlink($oldImage);
                                        }
                                        $this->$key = $imageName;
                                    }
                                }
                            }
                        }
                        //End upload file                       
                    } elseif ($config['type'] == 'categories' && isset($config['tree']['use_checkbox']) && $config['tree']['use_checkbox'] || $config['type'] == 'checkbox')
                        $this->$key = implode(',', Tools::getValue($key));
                    else
                        $this->$key = trim(Tools::getValue($key));
                }
            }
        }
        if (!count($errors)) {
            if ($this->id && $this->update() || !$this->id && $this->add()) {
                $success[] = Ets_faq::$trans['data_saved'];
            } else
                $errors[] = Ets_faq::$trans['unkown_error'];
        }
        return array('errors' => $errors, 'success' => $success);
    }
}