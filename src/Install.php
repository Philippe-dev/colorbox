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

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;

class Install
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = My::settings();

        $opts = [
            'transition'     => 'elastic',
            'speed'          => '350',
            'title'          => '',
            'width'          => '',
            'height'         => '',
            'innerWidth'     => '',
            'innerHeight'    => '',
            'initialWidth'   => '300',
            'initialHeight'  => '100',
            'maxWidth'       => '',
            'maxHeight'      => '',
            'scalePhotos'    => true,
            'scrolling'      => true,
            'iframe'         => false,
            'opacity'        => '0.85',
            'open'           => false,
            'preloading'     => true,
            'overlayClose'   => true,
            'loop'           => true,
            'slideshow'      => false,
            'slideshowSpeed' => '2500',
            'slideshowAuto'  => false,
            'slideshowStart' => __('Start slideshow'),
            'slideshowStop'  => __('Stop slideshow'),
            'current'        => __('{current} of {total}'),
            'previous'       => __('previous'),
            'next'           => __('next'),
            'close'          => __('close'),
            'onOpen'         => '',
            'onLoad'         => '',
            'onComplete'     => '',
            'onCleanup'      => '',
            'onClosed'       => '',
        ];

        $settings->put('colorbox_enabled', true, App::blogWorkspace()::NS_BOOL, 'Enable Colorbox plugin', false, true);
        $settings->put('colorbox_theme', 3, App::blogWorkspace()::NS_INT, 'Colorbox theme', false, true);
        $settings->put('colorbox_zoom_icon', false, App::blogWorkspace()::NS_BOOL, 'Enable Colorbox zoom icon', false, true);
        $settings->put('colorbox_zoom_icon_permanent', false, App::blogWorkspace()::NS_BOOL, 'Enable permanent Colorbox zoom icon', false, true);
        $settings->put('colorbox_position', false, App::blogWorkspace()::NS_BOOL, 'Colorbox zoom icon position', false, true);
        $settings->put('colorbox_user_files', true, App::blogWorkspace()::NS_BOOL, 'Colorbox user files in public folder', false, true);
        $settings->put('colorbox_selectors', '', App::blogWorkspace()::NS_STRING, 'Colorbox selectors', false, true);
        $settings->put('colorbox_legend', 'alt', App::blogWorkspace()::NS_STRING, 'Colorbox legend', false, true);
        $settings->put('colorbox_advanced', serialize($opts), App::blogWorkspace()::NS_STRING, 'Colorbox advanced options', false, true);

        return true;
    }
}
