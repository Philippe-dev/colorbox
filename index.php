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

if (!dcCore::app()->auth->check('admin', dcCore::app()->blog->id)) {
    return;
}

//Lightbox enabled test

if (dcCore::app()->plugins->moduleExists('lightbox')) {
    if (dcCore::app()->blog->settings->lightbox->lightbox_enabled) {
        dcCore::app()->error->add(__('Lightbox plugin is enabled. Please disable it before using Colorbox.'));
        return;
    }
}

//Settings
$s = dcCore::app()->blog->settings->colorbox;

// Init var
$p_url = 'plugin.php?p=' . basename(dirname(__FILE__));
$default_tab = isset($_GET['tab']) ? $_GET['tab'] : 'modal';
$themes = [
    '1' => __('Dark Mac'),
    '2' => __('Simple White'),
    '3' => __('Lightbox Classic'),
    '4' => __('White Mac'),
    '5' => __('Thick Grey'),
    '6' => __('Vintage Lightbox'),
];

// Saving configurations
if (isset($_POST['save'])) {
    $type = $_POST['type'];

    dcCore::app()->blog->triggerBlog();

    if ($type === 'modal') {
        $s->put('colorbox_enabled', !empty($_POST['colorbox_enabled']));

        if (isset($_POST['colorbox_theme'])) {
            $s->put('colorbox_theme', $_POST['colorbox_theme']);
        }

        http::redirect($p_url . '&upd=1');
    } elseif ($type === 'zoom') {
        $s->put('colorbox_zoom_icon', !empty($_POST['colorbox_zoom_icon']));
        $s->put('colorbox_zoom_icon_permanent', !empty($_POST['colorbox_zoom_icon_permanent']));
        $s->put('colorbox_position', !empty($_POST['colorbox_position']));

        http::redirect($p_url . '&tab=zoom&upd=2');
    } elseif ($type === 'advanced') {
        $opts = [
            'transition' => $_POST['transition'],
            'speed' => !empty($_POST['speed']) ? $_POST['speed'] : '350',
            'title' => $_POST['title'],
            'width' => $_POST['width'],
            'height' => $_POST['height'],
            'innerWidth' => $_POST['innerWidth'],
            'innerHeight' => $_POST['innerHeight'],
            'initialWidth' => !empty($_POST['initialWidth']) ? $_POST['initialWidth'] : '300',
            'initialHeight' => !empty($_POST['initialHeight']) ? $_POST['initialHeight'] : '100',
            'maxWidth' => $_POST['maxWidth'],
            'maxHeight' => $_POST['maxHeight'],
            'scalePhotos' => !empty($_POST['scalePhotos']),
            'scrolling' => !empty($_POST['scrolling']),
            'iframe' => !empty($_POST['iframe']),
            'opacity' => !empty($_POST['opacity']) ? $_POST['opacity'] : '0.85',
            'open' => !empty($_POST['open']),
            'preloading' => !empty($_POST['preloading']),
            'overlayClose' => !empty($_POST['overlayClose']),
            'loop' => !empty($_POST['loop']),
            'slideshow' => !empty($_POST['slideshow']),
            'slideshowSpeed' => !empty($_POST['slideshowSpeed']) ? $_POST['slideshowSpeed'] : '2500',
            'slideshowAuto' => !empty($_POST['slideshowAuto']),
            'slideshowStart' => $_POST['slideshowStart'],
            'slideshowStop' => $_POST['slideshowStop'],
            'current' => $_POST['current'],
            'previous' => $_POST['previous'],
            'next' => $_POST['next'],
            'close' => $_POST['close'],
            'onOpen' => $_POST['onOpen'],
            'onLoad' => $_POST['onLoad'],
            'onComplete' => $_POST['onComplete'],
            'onCleanup' => $_POST['onCleanup'],
            'onClosed' => $_POST['onClosed']
        ];

        $s->put('colorbox_advanced', serialize($opts));
        $s->put('colorbox_selectors', $_POST['colorbox_selectors']);
        $s->put('colorbox_user_files', $_POST['colorbox_user_files']);
        $s->put('colorbox_legend', $_POST['colorbox_legend']);
        http::redirect($p_url . '&tab=advanced&upd=3');
    }
}

?>

<html>
<head>
	<title><?php echo(__('Colorbox')); ?></title>
	<script>
	$(document).ready(function() {
		$("input[type=radio][name=colorbox_theme]").click(function() {
			var p = $(this).attr('value');
			$("img#thumbnail").attr('src','index.php?pf=colorbox/themes/'+p+'/images/thumbnail.jpg');
		});
		$("#colorbox_zoom_icon").click(function() {
			if (!$("#colorbox_zoom_icon").is(":checked")) {
				$("#colorbox_zoom_icon_permanent").attr('checked', false);
			}
		});
	});
	</script>
	<?php echo dcPage::jsPageTabs($default_tab) .
    dcPage::jsConfirmClose('modal-form') .
    dcPage::jsConfirmClose('zoom-form') .
    dcPage::jsConfirmClose('advanced-form')
    ; ?>
	<style type="text/css">
	
	#thumbnail { border: 1px solid #ccc; }
	</style>
</head>
<body>

<?php

// Baseline

$page_title = __('Colorbox');

echo dcPage::breadcrumb(
    [html::escapeHTML(dcCore::app()->blog->name) => '',
        '<span class="page-title">' . $page_title . '</span>' => ''
    ]
);

// Display messages

if (isset($_GET['upd'])) {
    $a_msg = [
        __('Modal window configuration successfully saved'),
        __('Zoom icon configuration successfully saved'),
        __('Advanced configuration successfully saved')
    ];

    $k = (integer) $_GET['upd'] - 1;

    if (array_key_exists($k, $a_msg)) {
        dcPage::success($a_msg[$k]);
    }
}

// Activation and theme tab
$theme_choice = '';
foreach ($themes as $k => $v) {
    $theme_choice .= '<p><label class="classic" for="colorbox_theme-' . $k . '">' .
    form::radio(['colorbox_theme', 'colorbox_theme-' . $k], $k, $s->colorbox_theme == $k) .
    ' ' . $v . '</label></p>';
}
$thumb_url = 'index.php?pf=colorbox/themes/' . $s->colorbox_theme . '/images/thumbnail.jpg';

echo
'<div class="multi-part" id="modal" title="' . __('Modal Window') . '">' .
    '<form action="' . $p_url . '" method="post" id="modal-form">' .
    '<div class="fieldset"><h3>' . __('Activation') . '</h3>' .
        '<p><label class="classic" for="colorbox_enabled">' .
        form::checkbox('colorbox_enabled', '1', $s->colorbox_enabled) .
        __('Enable Colorbox on this blog') . '</label></p>' .
    '</div>' .
    '<div class="fieldset"><h3>' . __('Theme') . '</h3>' .

        '<div class="two-cols clearfix">' .
            '<div class="col">' .
                '<p class="classic">' . __('Choose your theme for Colorbox:') . '</p>' .
                $theme_choice .

            '</div>' .
            '<div class="col">' .
                '<p><img id="thumbnail" src="' . $thumb_url . '" alt="' . __('Preview') . '" title="' . __('Preview') . '" width="400" height="204" /></p>' .
            '</div>' .

        '</div>' .
        '<p class="form-note info maximal">' . __('All themes may be customized, see <em>Personal files</em> help section.') . '</p>' .
    '</div>' .
    '<p>' . form::hidden(['type'], 'modal') . '</p>' .
    '<p class="clear"><input type="submit" name="save" value="' . __('Save configuration') . '" />' . dcCore::app()->formNonce() . '</p>' .
'</form>' .
'</div>';

// Zoom icon tab

echo
'<div class="multi-part" id="zoom" title="' . __('Zoom Icon') . '">' .
    '<form action="' . $p_url . '" method="post"  id="zoom-form">' .

        '<div class="fieldset"><h3>' . __('Behavior') . '</h3>' .
            '<p><label class="classic" for="colorbox_zoom_icon">' .
            form::checkbox('colorbox_zoom_icon', '1', $s->colorbox_zoom_icon) .
            __('Enable zoom icon on hovered thumbnails') . '</label></p>' .
            '<p><label class="classic" for="colorbox_zoom_icon_permanent">' .
            form::checkbox('colorbox_zoom_icon_permanent', '1', $s->colorbox_zoom_icon_permanent) .
            __('Always show zoom icon on thumbnails') . '</label></p>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Position') . '</h3>' .
            '<p><label class="classic" for="colorbox_position-1">' .
            form::radio(['colorbox_position', 'colorbox_position-1'], true, $s->colorbox_position) .
            __('on the left') . '</label></p>' .
            '<p><label class="classic" for="colorbox_position-2">' .
            form::radio(['colorbox_position', 'colorbox_position-2'], false, !$s->colorbox_position) .
            __('on the right') . '</label></p>' .
        '</div>' .
        '<p>' . form::hidden(['type'], 'zoom') . '</p>' .
        '<p class="clear"><input type="submit" name="save" value="' . __('Save configuration') . '" />' . dcCore::app()->formNonce() . '</p>' .
    '</form>' .
'</div>';

// Advanced tab

$effects = [
    __('Elastic') => 'elastic',
    __('Fade') => 'fade',
    __('No transition') => 'none'
];

$colorbox_legend = [
    __('Image alt attribute') => 'alt',
    __('Link title attribute') => 'title',
    __('No legend') => 'none'
];

$as = unserialize($s->colorbox_advanced);
echo
'<div class="multi-part" id="advanced" title="' . __('Advanced configuration') . '">' .
    '<form action="' . $p_url . '" method="post"  id="advanced-form">' .
        '<div class="fieldset"><h3>' . __('Personnal files') . '</h3>' .
            '<p>' . __('Store personnal CSS and image files in:') . '</p>' .
            '<p><label for="colorbox_user_files-1">' .
            form::radio(['colorbox_user_files', 'colorbox_user_files-1'], true, $s->colorbox_user_files) .
            __('public folder') . '</label></p>' .
            '<p><label for="colorbox_user_files-2">' .
            form::radio(['colorbox_user_files', 'colorbox_user_files-2'], false, !$s->colorbox_user_files) .
            __('theme folder') . '</label></p>' .

        '</div>' .
        '<div class="fieldset"><h3>' . __('Selectors') . '</h3>' .
            '<p><label class="maximal" for="colorbox_selectors">' . __('Apply Colorbox to the following supplementary selectors (ex: #sidebar,#pictures):') .
            '<br />' . form::field('colorbox_selectors', 80, 255, $s->colorbox_selectors) .
            '</label></p>' .
            '<p class="info">' . __('Leave blank to default: (.post)') . '</p>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Effects') . '</h3>' .
        '<div class="two-cols clearfix"><div class="col">' .
            '<p class="field"><label for="transition">' . __('Transition type') . '&nbsp;' .
            form::combo('transition', $effects, $as['transition']) .
            '</label></p>' .
            '<p class="field"><label for="speed">' . __('Transition speed') . '&nbsp;' .
            form::field('speed', 30, 10, $as['speed']) .
            '</label></p>' .
            '<p class="field"><label for="opacity">' . __('Opacity') . '&nbsp;' .
            form::field('opacity', 30, 10, $as['opacity']) .
            '</label></p>' .
            '<p><label for="open">' .
            form::checkbox('open', 1, $as['open']) .
            __('Auto open Colorbox') . '</label></p>' .
            '<p><label for="preloading">' .
            form::checkbox('preloading', 1, $as['preloading']) .
            __('Enable preloading for photo group') . '</label></p>' .
            '<p><label for="overlayClose">' .
            form::checkbox('overlayClose', 1, $as['overlayClose']) .
            __('Enable close by clicking on overlay') . '</label></p>' .
        '</div><div class="col">' .
            '<p><label for="slideshow">' .
            form::checkbox('slideshow', 1, $as['slideshow']) .
            __('Enable slideshow') . '</label></p>' .
            '<p><label for="slideshowAuto">' .
            form::checkbox('slideshowAuto', 1, $as['slideshowAuto']) .
            __('Auto start slideshow') . '</label></p>' .
            '<p class="field"><label for="slideshowSpeed">' . __('Slideshow speed') . '&nbsp;' .
            form::field('slideshowSpeed', 30, 10, $as['slideshowSpeed']) .
            '</label></p>' .
            '<p class="field"><label for="slideshowStart">' . __('Slideshow start display text') . '&nbsp;' .
            form::field('slideshowStart', 30, 255, $as['slideshowStart']) .
            '</label></p>' .
            '<p class="field"><label for="slideshowStop">' . __('Slideshow stop display text') . '&nbsp;' .
            form::field('slideshowStop', 30, 255, $as['slideshowStop']) .
            '</label></p>' .
        '</div></div>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Modal window') . '</h3>' .
        '<div class="two-cols clearfix"><div class="col">' .

            '<p class="field"><label for="colorbox_legend">' . __('Images legend') . '&nbsp;' .
            form::combo('colorbox_legend', $colorbox_legend, $s->colorbox_legend) .
            '</label></p>' .
            '<p class="field"><label for="title">' . __('Default legend') . '&nbsp;' .
            form::field('title', 30, 255, $as['title']) .
            '</label></p>' .
            '<p><label for="loop">' .
            form::checkbox('loop', 1, $as['loop']) .
            __('Loop on slideshow images') . '</label></p>' .
            '<p><label for="iframe">' .
            form::checkbox('iframe', 1, $as['iframe']) .
            __('Display content in  an iframe') . '</label></p>' .

        '</div><div class="col">' .
            '<p class="field"><label for="current">' . __('Current text') . '&nbsp;' .
            form::field('current', 30, 255, $as['current']) .
            '</label></p>' .
            '<p class="field"><label for="previous">' . __('Previous text') . '&nbsp;' .
            form::field('previous', 30, 255, $as['previous']) .
            '</label></p>' .
            '<p class="field"><label for="next">' . __('Next text') . '&nbsp;' .
            form::field('next', 30, 255, $as['next']) .
            '</label></p>' .
            '<p class="field"><label for="close">' . __('Close text') . '&nbsp;' .
            form::field('close', 30, 255, $as['close']) .
            '</label></p>' .
        '</div></div>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Dimensions') . '</h3>' .
        '<div class="two-cols clearfix"><div class="col">' .
            '<p class="field"><label for="width">' . __('Fixed width') . '&nbsp;' .
            form::field('width', 30, 10, $as['width']) .
            '</label></p>' .
            '<p class="field"><label for="height">' . __('Fixed height') . '&nbsp;' .
            form::field('height', 30, 10, $as['height']) .
            '</label></p>' .
            '<p class="field"><label for="innerWidth">' . __('Fixed inner width') . '&nbsp;' .
            form::field('innerWidth', 30, 10, $as['innerWidth']) .
            '</label></p>' .
            '<p class="field"><label for="innerHeight">' . __('Fixed inner height') . '&nbsp;' .
            form::field('innerHeight', 30, 10, $as['innerHeight']) .
            '</label></p>' .
            '<p><label for="scalePhotos">' .
            form::checkbox('scalePhotos', 1, $as['scalePhotos']) .
            __('Scale photos') . '</label></p>' .
            '<p><label class="classic" for="scrolling">' .
            form::checkbox('scrolling', 1, $as['scrolling']) .
            __('Show overflowing content') . '</label></p>' .
        '</div><div class="col">' .
            '<p class="field"><label for="initialWidth">' . __('Initial width') . '&nbsp;' .
            form::field('initialWidth', 30, 10, $as['initialWidth']) .
            '</label></p>' .
            '<p class="field"><label for="initialHeight">' . __('Initial height') . '&nbsp;' .
            form::field('initialHeight', 30, 10, $as['initialHeight']) .
            '</label></p>' .
            '<p class="field"><label for="maxWidth">' . __('Max width') . '&nbsp;' .
            form::field('maxWidth', 30, 10, $as['maxWidth']) .
            '</label></p>' .
            '<p class="field"><label for="maxHeight">' . __('Max height') . '&nbsp;' .
            form::field('maxHeight', 30, 10, $as['maxHeight']) .
            '</label></p>' .
        '</div></div>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Javascript') . '</h3>' .
        '<div class="two-cols clearfix"><div class="col">' .
            '<p class="field"><label for="onOpen">' . __('onOpen callback') . '&nbsp;' .
            form::field('onOpen', 80, 255, $as['onOpen'], 'maximal') .
            '</label></p>' .
            '<p class="field"><label for="onLoad">' . __('onLoad callback') . '&nbsp;' .
            form::field('onLoad', 80, 255, $as['onLoad'], 'maximal') .
            '</label></p>' .
            '<p class="field"><label for="onComplete">' . __('onComplete callback') . '&nbsp;' .
            form::field('onComplete', 80, 255, $as['onComplete'], 'maximal') .
            '</label></p>' .
        '</div><div class="col">' .
            '<p class="field"><label for="onCleanup">' . __('onCleanup callback') . '&nbsp;' .
            form::field('onCleanup', 80, 255, $as['onCleanup'], 'maximal') .
            '</label></p>' .
            '<p class="field"><label for="onClosed">' . __('onClosed callback') . '&nbsp;' .
            form::field('onClosed', 80, 255, $as['onClosed'], 'maximal') .
            '</label></p>' .
        '</div></div>' .
        '</div>' .
        '<p>' . form::hidden(['type'], 'advanced') . '</p>' .
        '<p class="clear"><input type="submit" name="save" value="' . __('Save configuration') . '" />' . dcCore::app()->formNonce() . '</p>' .
    '</form>' .
'</div>';

dcPage::helpBlock('colorbox');

?>

</body>
</html>