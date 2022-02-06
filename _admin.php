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

$_menu['Blog']->addItem(
    __('Colorbox'),
    $core->adminurl->get('admin.plugin.colorbox'),
    [dcPage::getPF('colorbox/icon.svg')],
    preg_match('/' . preg_quote($core->adminurl->get('admin.plugin.colorbox')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('usage,contentadmin', $core->blog->id)
);

$core->addBehavior(
    'adminDashboardFavorites',
    function ($core, $favs) {
        $favs->register('colorbox', [
            'title' => __('Colorbox'),
            'url' => $core->adminurl->get('admin.plugin.colorbox'),
            'small-icon' => [dcPage::getPF('colorbox/icon.svg')],
            'large-icon' => [dcPage::getPF('colorbox/icon.svg')],
            'permissions' => 'usage,contentadmin',
        ]);
    }
);
