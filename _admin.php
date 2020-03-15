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

if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$_menu['Blog']->addItem(

    __('Colorbox'),
    'plugin.php?p=colorbox',
    'index.php?pf=colorbox/icon.png',
    preg_match('/plugin.php\?p=colorbox(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('admin', $core->blog->id)
);

$core->addBehavior('adminDashboardFavs', array('colorboxBehaviors','dashboardFavs'));
 
class colorboxBehaviors
{
    public static function dashboardFavs($core, $favs)
    {
        $favs['colorbox'] = new ArrayObject(array(
            'colorbox',
            __('Colorbox'),
            'plugin.php?p=colorbox',
            'index.php?pf=colorbox/icon.png',
            'index.php?pf=colorbox/icon-big.png',
            'usage,contentadmin',
            null,
            null));
    }
}
