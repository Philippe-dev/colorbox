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
use Dotclear\Helper\File\Path;

class Frontend
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::behavior()->addBehavior('publicHeadContent', [self::class, 'publicHeadContent']);
        App::behavior()->addBehavior('publicFooterContent', [self::class, 'publicFooterContent']);

        return true;
    }

    public static function publicHeadContent()
    {
        if (!My::settings()->colorbox_enabled) {
            return;
        }

        echo
        My::cssLoad('colorbox_common.css') .
        My::cssLoad('/themes/' . My::settings()->colorbox_theme . '/colorbox_theme.css');

        if (My::settings()->colorbox_user_files) {
            $public_path        = App::blog()->public_path;
            $public_url         = App::blog()->settings->system->public_url;
            $colorbox_user_path = $public_path . '/colorbox/themes/';
            $colorbox_user_url  = $public_url . '/colorbox/themes/';

            if (file_exists($colorbox_user_path . My::settings()->colorbox_theme . '/colorbox_user.css')) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . My::settings()->colorbox_theme . '/colorbox_user.css">' . "\n";
            }
        } else {
            $theme_path         = Path::fullFromRoot(App::blog()->settings->system->themes_path . '/' . App::blog()->settings->system->theme, DC_ROOT);
            $theme_url          = App::blog()->settings->system->themes_url . '/' . App::blog()->settings->system->theme;
            $colorbox_user_path = $theme_path . '/colorbox/themes/' . My::settings()->colorbox_theme . '/colorbox_user.css';
            $colorbox_user_url  = $theme_url . '/colorbox/themes/' . My::settings()->colorbox_theme . '/colorbox_user.css';
            if (file_exists($colorbox_user_path)) {
                echo
                '<link rel="stylesheet" type="text/css" href="' . $colorbox_user_url . '">' . "\n";
            }
        }
    }

    public static function publicFooterContent($core)
    {
        // Settings

        if (!My::settings()->colorbox_enabled) {
            return;
        }

        $icon_name   = 'zoom.png';
        $icon_width  = '16';
        $icon_height = '16';
        $url         = App::blog()->getQmarkURL() . 'pf=' . My::id();

        echo
        My::jsLoad('jquery.colorbox.min.js') .
        '<script>' . "\n" .
        "//<![CDATA[\n";

        $selectors = '.post' . (My::settings()->colorbox_selectors !== '' ? ',' . My::settings()->colorbox_selectors : '');

        echo
        '$(function () {' . "\n" .
            'var count = 0; ' .
            '$("' . $selectors . '").each(function() {' . "\n" .
                'count++;' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').addClass("colorbox_zoom");' . "\n" .
                '$(this).find(\'a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],' .
                'a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"],a[href$=".GIF"]\').attr("rel", "colorbox-"+count);' . "\n";

        if (My::settings()->colorbox_zoom_icon_permanent) {
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

            if (My::settings()->colorbox_position) {
                echo 'var left = offset.left-parentleft;' . "\n";
            } else {
                echo 'var left = offset.left-parentleft+p.outerWidth()-' . $icon_width . ';' . "\n";
            }

            echo '$(this).append("<span style=\"z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;top:' . '"+top+"' . 'px;left:' . '"+left+"' . 'px;background: url(' . My::fileURL('/themes/' . My::settings()->colorbox_theme) . '/images/zoom.png) top left no-repeat; position:absolute;\"></span>");' . "\n" .
                '}' . "\n" .
            '});' . "\n";
        }

        if (My::settings()->colorbox_zoom_icon && !My::settings()->colorbox_zoom_icon_permanent) {
            echo
            '$(\'body\').prepend(\'<img id="colorbox_magnify" style="display:block;padding:0;margin:0;z-index:10;width:' . $icon_width . 'px;height:' . $icon_height . 'px;position:absolute;top:0;left:0;display:none;" src="' . My::fileURL('/themes/' . My::settings()->colorbox_theme) . '/images/zoom.png" alt="">\');' . "\n" .
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

            if (!My::settings()->colorbox_position) {
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

        foreach (unserialize(My::settings()->colorbox_advanced) as $k => $v) {
            if ($v === '') {
                if ($k == 'title' && My::settings()->colorbox_legend == 'alt') {
                    $opts[] = $k . ': function(){return $(this).find(\'img\').attr(\'alt\');}';
                } elseif ($k == 'title' && My::settings()->colorbox_legend == 'title') {
                    $opts[] = $k . ': function(){return $(this).attr(\'title\');}';
                } elseif ($k == 'title' && My::settings()->colorbox_legend == 'description') {
                    $opts[] = $k . ': function(){if ($(this).parent().prop(\'tagName\') === \'FIGURE\'){return $(this).next(\'figcaption\').text();} else return $(this).find(\'img\').attr(\'alt\');}';
                } elseif ($k == 'title' && My::settings()->colorbox_legend == 'none') {
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
