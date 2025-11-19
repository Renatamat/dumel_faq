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
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <rect width="{$width}" height="{$height}" fill="#fff"/>
    {foreach $lines as $line}
        <line x1="{$line.x1}" y1="{$line.y1}" x2="{$line.x2}" y2="{$line.y2}" stroke="rgba(0,0,0,0.2)" stroke-width="1"/>
    {/foreach}
    {foreach $dots as $dot}
        <circle cx="{$dot.cx}" cy="{$dot.cy}" r="{$dot.r}" fill="rgba(0,0,0,0.2)"/>
    {/foreach}
    {foreach $ellipses as $ellipse}
        <ellipse cx="{$ellipse.cx}" cy="{$ellipse.cy}" rx="{$ellipse.rx}" ry="{$ellipse.ry}" fill="{$ellipse.fill}"/>
    {/foreach}
    <rect x="0" y="0" width="{$width}" height="{$height}" fill="none" stroke="#000" stroke-width="1"/>
    {foreach $chars as $char}
        <g transform="rotate({$char.angle} {$char.x} {$char.y})">
            <text x="{$char.x}" y="{$char.y}" font-size="{$char.font_size}" fill="#222" font-family="Arial, sans-serif">{$char.char}</text>
        </g>
    {/foreach}
</svg>

