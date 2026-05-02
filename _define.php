<?php
/**
 * @brief Colorbox, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Philippe aka amalgame and Tomtom
 *
 * @copyright Philippe Hénaff philippe@dissitou.org
 * @copyright GPL-2.0 [https://www.gnu.org/licenses/gpl-2.0.html]
 */
$this->registerModule(
    'Colorbox',
    'A lightweight customizable lightbox',
    'Philippe aka amalgame and Tomtom',
    '6.8',
    [
        'date'        => '2026-05-02T00:00:08+0100',
        'requires'    => [['core', '2.37']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/Philippe-dev/colorbox',
    ]
);
