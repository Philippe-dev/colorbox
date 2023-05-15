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
use dcUtils;
use dcNsProcess;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\File\Path;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::FRONTEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehavior('publicHeadContent', [self::class, 'publicHeadContent']);
        dcCore::app()->addBehavior('publicFooterContent', [self::class, 'publicFooterContent']);

        return true;
    }

    public static function publicHeadContent()
    {
        // Settings

        $settings = dcCore::app()->blog->settings->get(My::id());

        if (!$settings->colorbox_enabled) {
            return;
        }

        echo
        dcUtils::cssModuleLoad(My::id() . '/css/colorbox_common.css') .
        dcUtils::cssModuleLoad(My::id() . '/themes/' . $settings->colorbox_theme . '/colorbox_theme.css');

        if ($settings->colorbox_user_files) {
            $public_path        = dcCore::app()->blog->public_path;
            $public_url         = dcCore::app()->blog->settings->system->public_url;
            $colorbox_user_path = $public_path . '/colorbox/themes/';
            $colorbox_user_url  = $public_url . '/colorbox/themes/';

            if (file_exists($colorbox_user_path . $settings->colorbox_theme . '/colorbox_user.css')) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . $settings->colorbox_theme . '/colorbox_user.css" />' . "\n";
            }
        } else {
            $theme_path         = Path::fullFromRoot(dcCore::app()->blog->settings->system->themes_path . '/' . dcCore::app()->blog->settings->system->theme, DC_ROOT);
            $theme_url          = dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme;
            $colorbox_user_path = $theme_path . '/colorbox/themes/' . $settings->colorbox_theme . '/colorbox_user.css';
            $colorbox_user_url  = $theme_url . '/colorbox/themes/' . $settings->colorbox_theme . '/colorbox_user.css';
            if (file_exists($colorbox_user_path)) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . '" />' . "\n";
            }
        }
    }

    public static function publicFooterContent($core)
    {
        // Settings

        $settings = dcCore::app()->blog->settings->get(My::id());

        if (!$settings->colorbox_enabled) {
            return;
        }

        $icon_name   = 'zoom.png';
        $icon_width  = '16';
        $icon_height = '16';

        echo
        dcUtils::jsModuleLoad(My::id() . '/js/jquery.colorbox-min.js') .
        '<script>' . "\n" .
        "//<![CDATA[\n";

        $selectors = '.post' . ($settings->colorbox_selectors !== '' ? ',' . $settings->colorbox_selectors : '');

        echo
        '$(function () {' . "\n" .
            'var count = 0; ' .
            '$("' . $selectors . '").each(function() {' . "\n" .
                'count++;' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').addClass("colorbox_zoom");' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').attr("rel", "colorbox-"+count);' . "\n";

        if ($settings->colorbox_zoom_icon_permanent) {
            echo
            '$(this).find("a.colorbox_zoom").each(function(){' . "\n" .
                'var p = $(this).find("img");' . "\n" .
                'if (p.length != 0){' . "\n" .
                    'var offset = p.offset();' . "\n" .
                    'var parent = p.offsetParent();' . "\n" .
                    'var offsetparent = parent.offset();' . "\n" .
                    'var parenttop = offsetparent.top;' . "\n" .
                    'var parentleft = offsetparent.left;' . "\n" .
                    'var top = offset.top-parenttop;' . "\n";

            if ($settings->colorbox_position) {
                echo 'var left = offset.left-parentleft;' . "\n";
            } else {
                echo 'var left = offset.left-parentleft+p.outerWidth()-' . $icon_width . ';' . "\n";
            }

            echo '$(this).append("<span style=\"z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;top:' . '"+top+"' . 'px;left:' . '"+left+"' . 'px;background: url(' . Html::escapeJS($url) . '/themes/' . $settings->colorbox_theme . '/images/zoom.png) top left no-repeat; position:absolute;\"></span>");' . "\n" .
                '}' . "\n" .
            '});' . "\n";
        }

        if ($settings->colorbox_zoom_icon && !$settings->colorbox_zoom_icon_permanent) {
            echo
            '$(\'body\').prepend(\'<img id="colorbox_magnify" style="display:block;padding:0;margin:0;z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;position:absolute;top:0;left:0;display:none;" src="' . Html::escapeJS($url) . '/themes/' . $settings->colorbox_theme . '/images/zoom.png" alt=""  />\');' . "\n" .
            '$(\'img#colorbox_magnify\').on(\'click\', function ()' . "\n" .
                '{ ' . "\n" .
                    '$("a.colorbox_zoom img.colorbox_hovered").click(); ' . "\n" .
                    '$("a.colorbox_zoom img.colorbox_hovered").removeClass(\'colorbox_hovered\');' . "\n" .
                '});' . "\n" .
                '$(\'a.colorbox_zoom img\').on(\'click\', function ()' . "\n" .
                '{ ' . "\n" .
                    '$(this).removeClass(\'colorbox_hovered\');' . "\n" .
                '});' . "\n" .
                '$("a.colorbox_zoom img").hover(function(){' . "\n" .

                'var p = $(this);' . "\n" .
                'p.addClass(\'colorbox_hovered\');' . "\n" .
                'var offset = p.offset();' . "\n";

            if (!$settings->colorbox_position) {
                echo '$(\'img#colorbox_magnify\').css({\'top\' : offset.top, \'left\' : offset.left+p.outerWidth()-' . $icon_width . '});' . "\n";
            } else {
                echo '$(\'img#colorbox_magnify\').css({\'top\' : offset.top, \'left\' : offset.left});' . "\n";
            }
            echo
            '$(\'img#colorbox_magnify\').show();' . "\n" .
            '},function(){' . "\n" .
                'var p = $(this);' . "\n" .
                'p.removeClass(\'colorbox_hovered\');' . "\n" .
                '$(\'img#colorbox_magnify\').hide();' . "\n" .
            '});' . "\n";
        }

        foreach (unserialize($settings->colorbox_advanced) as $k => $v) {
            if ($v === '') {
                if ($k == 'title' && $settings->colorbox_legend == 'alt') {
                    $opts[] = $k . ': function(){return $(this).find(\'img\').attr(\'alt\');}';
                } elseif ($k == 'title' && $settings->colorbox_legend == 'title') {
                    $opts[] = $k . ': function(){return $(this).attr(\'title\');}';
                } elseif ($k == 'title' && $settings->colorbox_legend == 'none') {
                    $opts[] = $k . ': \'\'';
                } else {
                    $opts[] = $k . ': false';
                }
            } elseif (is_bool($v)) {
                $opts[] = $k . ': ' . ($v ? 'true' : 'false');
            } elseif (is_numeric($v)) {
                $opts[] = $k . ': ' . $v;
            } elseif (is_string($v)) {
                if ($k == 'onOpen' || $k == 'onLoad' || $k == 'onComplete' || $k == 'onCleanup' || $k == 'onClosed') {
                    $opts[] = $k . ': function(){return ' . $v . '}';
                } else {
                    $opts[] = $k . ": '" . $v . "'";
                }
            }
        }

        echo
        "});\n" .
        '$("a[rel*=\'colorbox-\']").colorbox({' . implode(",\n", $opts) . '});' . "\n" .
        "});\n" .
        "\n//]]>\n" .
        "</script>\n";
    }
}
