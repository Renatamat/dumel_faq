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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ets_faqCaptchaModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $security_code = Tools::substr(sha1((string)mt_rand()), 17, 6);
        $context = Ets_faq::getInstanceContext();
        $captcha_name = Tools::getValue('captcha_name');
        if (!$captcha_name || !Validate::isCleanHtml($captcha_name))
            $captcha_name = 'security_faq';
        $context->cookie->__set($captcha_name, $security_code);
        $context->cookie->write();

        $width = 150;
        $height = 35;
        $theme = Tools::getValue('theme');

        // Prepare noise and character data
        $lines = [];
        for ($i = 0; $i < 8; $i++) {
            $lines[] = [
                'x1' => rand(0, $width),
                'y1' => rand(0, $height),
                'x2' => rand(0, $width),
                'y2' => rand(0, $height),
            ];
        }
        $dots = [];
        for ($i = 0; $i < 30; $i++) {
            $dots[] = [
                'cx' => rand(0, $width),
                'cy' => rand(0, $height),
                'r' => rand(1, 2),
            ];
        }
        $ellipses = [];
        if ($theme == 'colorful') {
            $colors = ['rgba(255,0,0,0.3)', 'rgba(0,255,0,0.3)', 'rgba(0,0,255,0.3)'];
            for ($i = 0; $i < 3; $i++) {
                $ellipses[] = [
                    'cx' => rand(5, 145),
                    'cy' => rand(0, 35),
                    'rx' => 15,
                    'ry' => 15,
                    'fill' => $colors[$i],
                ];
            }
        }
        $char_count = Tools::strlen($security_code);
        $start_x = 20;
        $gap = ($width - 40) / $char_count;
        $chars = [];
        for ($i = 0; $i < $char_count; $i++) {
            $chars[] = [
                'char' => htmlspecialchars($security_code[$i]),
                'angle' => rand(-25, 25),
                'font_size' => rand(16, 22),
                'x' => $start_x + $i * $gap + rand(-2, 2),
                'y' => rand(20, $height - 5),
            ];
        }

        // Assign variables to Smarty
        $this->context->smarty->assign([
            'width' => $width,
            'height' => $height,
            'lines' => $lines,
            'dots' => $dots,
            'ellipses' => $ellipses,
            'chars' => $chars,
            'theme' => $theme,
        ]);

        header('Content-Type: image/svg+xml');
        $this->context->smarty->display('module:ets_faq/views/templates/front/captcha_svg.tpl');
        exit();
    }
}