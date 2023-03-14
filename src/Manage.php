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

namespace Dotclear\Plugin\Colorbox;

use dcAuth;
use dcCore;
use dcNsProcess;
use dcPage;
use initPages;
use html;

class Manage extends dcNsProcess
{
    public static function init(): bool
    {
        if (defined('DC_CONTEXT_ADMIN')) {
            dcPage::check(dcCore::app()->auth->makePermissions([
                initPages::PERMISSION_PAGES,
                dcAuth::PERMISSION_CONTENT_ADMIN,
            ]));
        }

        if (dcCore::app()->plugins->moduleExists('lightbox')) {
            if (dcCore::app()->blog->settings->lightbox->lightbox_enabled) {
                dcCore::app()->error->add(__('Lightbox plugin is enabled. Please disable it before using Colorbox.'));

                return false;
            }
        }

        //Settings
        $s = dcCore::app()->blog->settings->colorbox;

        // Init var

        $default_tab = $_GET['tab'] ?? 'modal';
        $themes      = [
            '1' => __('Dark Mac'),
            '2' => __('Simple White'),
            '3' => __('Lightbox Classic'),
            '4' => __('White Mac'),
            '5' => __('Thick Grey'),
            '6' => __('Vintage Lightbox'),
        ];

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        if (isset($_POST['save'])) {
            $type = $_POST['type'];

            dcCore::app()->blog->triggerBlog();

            if ($type === 'modal') {
                $s->put('colorbox_enabled', !empty($_POST['colorbox_enabled']));

                if (isset($_POST['colorbox_theme'])) {
                    $s->put('colorbox_theme', $_POST['colorbox_theme']);
                }

                http::redirect(dcCore::app()->admin->getPageURL() . '&upd=1');
            } elseif ($type === 'zoom') {
                $s->put('colorbox_zoom_icon', !empty($_POST['colorbox_zoom_icon']));
                $s->put('colorbox_zoom_icon_permanent', !empty($_POST['colorbox_zoom_icon_permanent']));
                $s->put('colorbox_position', !empty($_POST['colorbox_position']));

                http::redirect(dcCore::app()->admin->getPageURL() . '&tab=zoom&upd=2');
            } elseif ($type === 'advanced') {
                $opts = [
                    'transition'     => $_POST['transition'],
                    'speed'          => !empty($_POST['speed']) ? $_POST['speed'] : '350',
                    'title'          => $_POST['title'],
                    'width'          => $_POST['width'],
                    'height'         => $_POST['height'],
                    'innerWidth'     => $_POST['innerWidth'],
                    'innerHeight'    => $_POST['innerHeight'],
                    'initialWidth'   => !empty($_POST['initialWidth']) ? $_POST['initialWidth'] : '300',
                    'initialHeight'  => !empty($_POST['initialHeight']) ? $_POST['initialHeight'] : '100',
                    'maxWidth'       => $_POST['maxWidth'],
                    'maxHeight'      => $_POST['maxHeight'],
                    'scalePhotos'    => !empty($_POST['scalePhotos']),
                    'scrolling'      => !empty($_POST['scrolling']),
                    'iframe'         => !empty($_POST['iframe']),
                    'opacity'        => !empty($_POST['opacity']) ? $_POST['opacity'] : '0.85',
                    'open'           => !empty($_POST['open']),
                    'preloading'     => !empty($_POST['preloading']),
                    'overlayClose'   => !empty($_POST['overlayClose']),
                    'loop'           => !empty($_POST['loop']),
                    'slideshow'      => !empty($_POST['slideshow']),
                    'slideshowSpeed' => !empty($_POST['slideshowSpeed']) ? $_POST['slideshowSpeed'] : '2500',
                    'slideshowAuto'  => !empty($_POST['slideshowAuto']),
                    'slideshowStart' => $_POST['slideshowStart'],
                    'slideshowStop'  => $_POST['slideshowStop'],
                    'current'        => $_POST['current'],
                    'previous'       => $_POST['previous'],
                    'next'           => $_POST['next'],
                    'close'          => $_POST['close'],
                    'onOpen'         => $_POST['onOpen'],
                    'onLoad'         => $_POST['onLoad'],
                    'onComplete'     => $_POST['onComplete'],
                    'onCleanup'      => $_POST['onCleanup'],
                    'onClosed'       => $_POST['onClosed'],
                ];

                $s->put('colorbox_advanced', serialize($opts));
                $s->put('colorbox_selectors', $_POST['colorbox_selectors']);
                $s->put('colorbox_user_files', $_POST['colorbox_user_files']);
                $s->put('colorbox_legend', $_POST['colorbox_legend']);
                http::redirect(dcCore::app()->admin->getPageURL() . '&tab=advanced&upd=3');
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::$init) {
            return;
        }

        echo dcPage::breadcrumb(
            [html::escapeHTML(dcCore::app()->blog->name)              => '',
                '<span class="page-title">' . $page_title . '</span>' => '',
            ]
        );
        
        dcPage::helpBlock('colorbox');

        dcPage::closeModule();
    }
}
