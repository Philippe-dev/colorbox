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

if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Colorbox'),
    dcCore::app()->adminurl->get('admin.plugin.colorbox'),
    [dcPage::getPF('colorbox/icon.svg'), dcPage::getPF('colorbox/icon-dark.svg')],
    preg_match('/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.colorbox')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check('usage,contentadmin', dcCore::app()->blog->id)
);

dcCore::app()->addBehavior(
    'adminDashboardFavorites',
    function ($core, $favs) {
        $favs->register('colorbox', [
            'title' => __('Colorbox'),
            'url' => dcCore::app()->adminurl->get('admin.plugin.colorbox'),
            'small-icon' => [dcPage::getPF('colorbox/icon.svg'), dcPage::getPF('colorbox/icon-dark.svg')],
            'large-icon' => [dcPage::getPF('colorbox/icon.svg'), dcPage::getPF('colorbox/icon-dark.svg')],
            'permissions' => 'usage,contentadmin',
        ]);
    }
);
