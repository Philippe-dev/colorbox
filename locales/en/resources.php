<?php
/**
 * @brief Colorbox, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Philippe aka amalgame and Tomtom
 *
 * @copyright GPL-2.0 [https://www.gnu.org/licenses/gpl-2.0.html]
 */
if (!isset(dcCore::app()->resources['help']['colorbox'])) {
    dcCore::app()->resources['help']['colorbox'] = dirname(__FILE__) . '/help/advanced_help.html';
}
