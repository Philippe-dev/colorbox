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
 
$m_version = $core->plugins->moduleInfo('colorbox', 'version');
 
$i_version = $core->getVersion('colorbox');
 
if (version_compare($i_version, $m_version, '>=')) {
    return;
}

# Settings
$core->blog->settings->addNamespace('colorbox');
$s =& $core->blog->settings->colorbox;

$opts = array(
    'transition' => 'elastic',
    'speed' => '350',
    'title' => '',
    'width' => '',
    'height' => '',
    'innerWidth' => '',
    'innerHeight' => '',
    'initialWidth' => '300',
    'initialHeight' => '100',
    'maxWidth' => '',
    'maxHeight' => '',
    'scalePhotos' => true,
    'scrolling' => true,
    'iframe' => false,
    'opacity' => '0.85',
    'open' => false,
    'preloading' => true,
    'overlayClose' => true,
    'loop' => true,
    'slideshow' => false,
    'slideshowSpeed' => '2500',
    'slideshowAuto' => false,
    'slideshowStart' => __('Start slideshow'),
    'slideshowStop' => __('Stop slideshow'),
    'current' => __('{current} of {total}'),
    'previous' => __('previous'),
    'next' => __('next'),
    'close' => __('close'),
    'onOpen' => '',
    'onLoad' => '',
    'onComplete' => '',
    'onCleanup' => '',
    'onClosed' => ''
);

$s->put('colorbox_enabled', false, 'boolean', 'Enable Colorbox plugin', false, true);
$s->put('colorbox_theme', '3', 'integer', 'Colorbox theme', false, true);
$s->put('colorbox_zoom_icon', false, 'boolean', 'Enable Colorbox zoom icon', false, true);
$s->put('colorbox_zoom_icon_permanent', false, 'boolean', 'Enable permanent Colorbox zoom icon', false, true);
$s->put('colorbox_position', false, 'boolean', 'Colorbox zoom icon position', false, true);
$s->put('colorbox_user_files', 'public', 'boolean', 'Colorbox user files', false, true);
$s->put('colorbox_selectors', '', 'string', 'Colorbox selectors', false, true);
$s->put('colorbox_legend', 'alt', 'string', 'Colorbox legend', false, true);
$s->put('colorbox_advanced', serialize($opts), 'string', 'Colorbox advanced options', false, true);

$core->setVersion('colorbox', $m_version);

return true;
