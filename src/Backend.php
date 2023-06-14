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

declare(strict_types=1);

namespace Dotclear\Plugin\colorbox;

use dcAdmin;
use dcCore;
use dcFavorites;
use dcPage;
use dcNsProcess;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'adminDashboardFavoritesV2' => function (dcFavorites $favs) {
                $favs->register(My::id(), [
                    'title'       => My::name(),
                    'url'         => My::manageUrl(),
                    'small-icon'  => My::icons(),
                    'large-icon'  => My::icons(),
                    'permissions' => dcCore::app()->auth->makePermissions([
                        dcCore::app()->auth::PERMISSION_ADMIN,
                    ]),
                ]);
            },
        ]);

        My::addBackendMenuItem(dcAdmin::MENU_BLOG);

        return true;
    }
}
