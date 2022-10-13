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

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Colorbox',
    'A lightweight customizable lightbox',
    'Philippe aka amalgame and Tomtom',
    '3.5',
    [
        'requires' => [['core', '2.23']],
        'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]),
        'priority' => 2000
    ]
);
