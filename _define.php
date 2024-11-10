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
    '5.1',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/Philippe-dev/colorbox',
    ]
);
