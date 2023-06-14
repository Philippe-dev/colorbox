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

use dcCore;
use Dotclear\Module\MyPlugin;

class My extends MyPlugin
{
    /**
     * Current admin page url
     */
    public static function url(): string
    {
        return dcCore::app()->admin->getPageURL();
    }
}
