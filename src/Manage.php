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
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Radio;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Exception;

class Manage
{
    use TraitProcess;

    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        self::status(My::checkContext(My::MANAGE));

        return self::status();
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $default_tab = $_GET['tab'] ?? 'modal';

        $themes = [
            '1' => __('Dark Mac'),
            '2' => __('Simple White'),
            '3' => __('Lightbox Classic'),
            '4' => __('White Mac'),
            '5' => __('Thick Grey'),
            '6' => __('Vintage Lightbox'),
        ];

        App::backend()->default_tab = $default_tab;
        App::backend()->themes      = $themes;

        if (!empty($_POST)) {
            try {
                $type = $_POST['type'];

                if ($type === 'modal') {
                    My::settings()->put('colorbox_enabled', !empty($_POST['colorbox_enabled']));

                    if (isset($_POST['colorbox_theme'])) {
                        My::settings()->put('colorbox_theme', $_POST['colorbox_theme']);
                    }
                    App::blog()->triggerBlog();
                    My::redirect(['upd' => 1]);
                } elseif ($type === 'zoom') {
                    My::settings()->put('colorbox_zoom_icon', !empty($_POST['colorbox_zoom_icon']));
                    My::settings()->put('colorbox_zoom_icon_permanent', !empty($_POST['colorbox_zoom_icon_permanent']));
                    My::settings()->put('colorbox_position', !empty($_POST['colorbox_position']));

                    App::blog()->triggerBlog();
                    My::redirect(['tab' => 'zoom', 'upd' => 2]);
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

                    My::settings()->put('colorbox_advanced', serialize($opts));
                    My::settings()->put('colorbox_selectors', $_POST['colorbox_selectors']);
                    My::settings()->put('colorbox_user_files', $_POST['colorbox_user_files']);
                    My::settings()->put('colorbox_legend', $_POST['colorbox_legend']);

                    App::blog()->triggerBlog();
                    My::redirect(['tab' => 'advanced', 'upd' => 3]);
                }
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        App::backend()->page()->openModule(
            My::name(),
            App::backend()->page()->jsPageTabs(App::backend()->default_tab) .
            App::backend()->page()->jsConfirmClose('modal-form') .
            App::backend()->page()->jsConfirmClose('zoom-form') .
            App::backend()->page()->jsConfirmClose('advanced-form') .
            '<script>' .
            '$(document).ready(function() {' .
                '$("input[type=radio][name=colorbox_theme]").click(function() {' .
                    'const url = `themes/${$(this).attr("value")}/images/thumbnail.jpg`;' .
                    '$("img#thumbnail").attr("src", `' . My::fileURL('${url}') . '` );' .
                '});' .
            '});' .
            '</script>' .
            '<style type="text/css">' .
                '#thumbnail { border: 1px solid #ccc; padding: 0.1em}' .
            '</style>'
        );

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name) => '',
                My::name()                          => '',
            ]
        ) .
        App::backend()->notices()->getNotices();

        if (isset($_GET['upd'])) {
            $a_msg = [
                __('Modal window configuration successfully saved'),
                __('Zoom icon configuration successfully saved'),
                __('Advanced configuration successfully saved'),
            ];

            $k = (int) $_GET['upd'] - 1;

            if (array_key_exists($k, $a_msg)) {
                App::backend()->notices()->success($a_msg[$k]);
            }
        }

        // Activation and theme tab
        $theme_choice = [];
        foreach (App::backend()->themes as $key => $value) {
            $theme_choice[] = (new Para())
            ->items([
                (new Radio(['colorbox_theme', 'colorbox_theme-' . $key], My::settings()->colorbox_theme === $key))
                    ->value($key)
                    ->label(new Label($value, Label::IL_FT)),
            ]);
        }

        $thumb_url = My::fileURL('/themes/' . My::settings()->colorbox_theme . '/images/thumbnail.jpg');

        //Modal window tab
        echo
        (new Div())
            ->class('multi-part')
            ->id('modal')
            ->title(__('Modal Window'))
            ->items([
                (new Form('modal-form'))
                ->action(My::manageUrl())
                ->method('post')
                ->fields([
                    (new Fieldset())
                        ->legend((new Legend(__('Activation'))))
                        ->fields([
                            (new Para())->items([
                                (new Checkbox('colorbox_enabled', (bool) My::settings()->colorbox_enabled)),
                                (new Label(__('Enable Colorbox on this blog'), Label::OUTSIDE_LABEL_AFTER))->for('colorbox_enabled')->class('classic'),
                            ]),
                        ]),
                    (new Fieldset())
                        ->legend((new Legend(__('Theme'))))
                        ->fields([
                            (new Div())
                            ->class(['two-boxes', 'odd'])
                            ->items([
                                (new Para())
                                ->class('classic')
                                ->items([
                                    (new Text(null, ' ' . __('Choose your theme for Colorbox:'))),
                                    ...$theme_choice,
                                ]),
                            ]),
                            (new Div())
                            ->class(['two-boxes', 'even'])
                            ->items([
                                (new Para())
                                    ->items([
                                        (new Img($thumb_url))
                                            ->id('thumbnail')
                                            ->title(__('Preview theme'))
                                            ->alt(__('Preview')),
                                    ]),
                            ]),
                            (new Note())
                                ->class(['form-note', 'info', 'maximal'])
                                ->text(__('All themes may be customized, see <em>Personal files</em> help section.')),
                        ]),
                    (new Hidden(['type'], 'modal')),
                    (new Input('save'))
                            ->type('submit')
                            ->value(__('Save configuration')),
                    App::nonce()->formNonce(),
                ]),
            ])->render();

        // Zoom icon tab
        echo
        (new Div())
            ->class('multi-part')
            ->id('zoom')
            ->title(__('Zoom Icon'))
            ->items([
                (new Form('zoom-form'))
                ->action(My::manageUrl())
                ->method('post')
                ->fields([
                    (new Fieldset())
                        ->legend((new Legend(__('Behaviour'))))
                        ->fields([
                            (new Para())->items([
                                (new Checkbox('colorbox_zoom_icon', (bool) My::settings()->colorbox_zoom_icon)),
                                (new Label(__('Enable zoom icon on hovered thumbnails'), Label::OUTSIDE_LABEL_AFTER))->for('colorbox_zoom_icon')->class('classic'),
                            ]),
                            (new Para())->items([
                                (new Checkbox('colorbox_zoom_icon_permanent', (bool) My::settings()->colorbox_zoom_icon_permanent)),
                                (new Label(__('Always show zoom icon on thumbnails'), Label::OUTSIDE_LABEL_AFTER))->for('colorbox_zoom_icon_permanent')->class('classic'),
                            ]),
                        ]),
                    (new Fieldset())
                        ->legend((new Legend(__('Icon position'))))
                        ->fields([
                            (new Radio(['colorbox_position', 'colorbox_position-1'], My::settings()->colorbox_position))
                                ->value(true)
                                ->label(new Label(__('on the left'), Label::IL_FT)),
                            (new Radio(['colorbox_position', 'colorbox_position-2'], !My::settings()->colorbox_position))
                                ->value(false)
                                ->label(new Label(__('on the right'), Label::IL_FT)),
                        ]),
                    (new Hidden(['type'], 'zoom')),
                    (new Input('save'))
                            ->type('submit')
                            ->value(__('Save configuration')),
                    App::nonce()->formNonce(),
                ]),
            ])
        ->render();

        $effects = [
            __('Elastic')       => 'elastic',
            __('Fade')          => 'fade',
            __('No transition') => 'none',
        ];

        $colorbox_legend = [
            __('Image alt attribute')  => 'alt',
            __('Link title attribute') => 'title',
            __('Image description')    => 'description',
            __('No legend')            => 'none',
        ];

        $as = unserialize(My::settings()->colorbox_advanced);

        // Advanced tab
        echo
        (new Div())
        ->class('multi-part')
        ->id('advanced')
        ->title(__('Advanced configuration'))
        ->items([
            (new Form('advanced-form'))
            ->action(My::manageUrl())
            ->method('post')
            ->fields([
                (new Fieldset())
                ->legend((new Legend(__('Personnal files'))))
                ->fields([
                    (new Para())
                        ->class('classic')
                        ->items([
                            (new Label(__('Store personnal CSS and image files in:'), Label::OUTSIDE_TEXT_BEFORE)),
                            (new Radio(['colorbox_user_files', 'colorbox_user_files-1'], My::settings()->colorbox_user_files))
                                ->value(true)
                                ->label(new Label(__('public folder'), Label::IL_FT)),
                            (new Radio(['colorbox_user_files', 'colorbox_user_files-2'], !My::settings()->colorbox_user_files))
                                ->value(false)
                                ->label(new Label(__('theme folder'), Label::IL_FT)),
                        ]),

                ]),
                (new Fieldset())
                ->legend((new Legend(__('Selectors'))))
                ->fields([
                    (new Para())
                        ->class('classic')
                        ->items([
                            (new Input('colorbox_selectors'))
                                ->size(80)
                                ->maxlength(255)
                                ->value(My::settings()->colorbox_selectors)
                                ->label((new Label(__('Apply Colorbox to the following supplementary selectors (ex: #sidebar,#pictures):'), Label::OUTSIDE_TEXT_BEFORE))),
                            (new Note())
                                ->class(['form-note', 'info', 'maximal'])
                                ->text(__('Leave blank to default: (.post)')),
                        ]),
                ]),
                (new Fieldset())
                ->legend((new Legend(__('Effects'))))
                ->fields([
                    (new Div())
                        ->class(['two-boxes', 'odd'])
                        ->items([
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Select('transition'))
                                ->items($effects)
                                ->default($as['transition'])
                                ->label(new Label(__('Transition type'), Label::OUTSIDE_LABEL_BEFORE)),
                            ]),
                            (new Para())
                                ->class('classic')
                                ->items([
                                    (new Input('speed'))
                                        ->size(30)
                                        ->maxlength(255)
                                        ->value($as['speed'])
                                        ->label((new Label(__('Transition speed'), Label::OUTSIDE_TEXT_BEFORE))),
                                ]),
                            (new Para())
                                ->class('classic')
                                ->items([
                                    (new Input('opacity'))
                                        ->size(30)
                                        ->maxlength(255)
                                        ->value($as['opacity'])
                                        ->label((new Label(__('Opacity'), Label::OUTSIDE_TEXT_BEFORE))),
                                ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('open', (bool) $as['open'])),
                                (new Label(__('Auto open Colorbox'), Label::OUTSIDE_LABEL_AFTER))->for('open')->class('classic'),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('preloading', (bool) $as['preloading'])),
                                (new Label(__('Enable preloading for photo group'), Label::OUTSIDE_LABEL_AFTER))->for('preloading')->class('classic'),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('overlayClose', (bool) $as['overlayClose'])),
                                (new Label(__('Enable close by clicking on overlay'), Label::OUTSIDE_LABEL_AFTER))->for('overlayClose')->class('classic'),
                            ]),
                        ]),

                    (new Div())
                    ->class(['two-boxes', 'even'])
                    ->items([
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('slideshow', (bool) $as['slideshow'])),
                                (new Label(__('Enable slideshow'), Label::OUTSIDE_LABEL_AFTER))->for('slideshow')->class('classic'),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('slideshowAuto', (bool) $as['slideshowAuto'])),
                                (new Label(__('Enable Auto start slideshow'), Label::OUTSIDE_LABEL_AFTER))->for('slideshowAuto')->class('classic'),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('slideshowSpeed'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['slideshowSpeed'])
                                    ->label((new Label(__('Slideshow speed'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('slideshowStart'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['slideshowStart'])
                                    ->label((new Label(__('Slideshow start display text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('slideshowStop'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['slideshowStop'])
                                    ->label((new Label(__('Slideshow stop display text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                    ]),
                ]),
                (new Fieldset())
                ->legend((new Legend(__('Modal window'))))
                ->fields([
                    (new Div())
                        ->class(['two-boxes', 'odd'])
                        ->items([
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Select('colorbox_legend'))
                                ->items($colorbox_legend)
                                ->default(My::settings()->colorbox_legend)
                                ->label(new Label(__('Images legend'), Label::OUTSIDE_LABEL_BEFORE)),
                            ]),
                            (new Para())
                                ->class('classic')
                                ->items([
                                    (new Input('title'))
                                        ->size(30)
                                        ->maxlength(255)
                                        ->value($as['title'])
                                        ->label((new Label(__('Default legend'), Label::OUTSIDE_TEXT_BEFORE))),
                                ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('loop', (bool) $as['loop'])),
                                (new Label(__('Loop on slideshow images'), Label::OUTSIDE_LABEL_AFTER))->for('loop')->class('classic'),
                            ]),
                            (new Para())
                                ->class('classic')
                                ->items([
                                    (new Checkbox('iframe', (bool) $as['iframe'])),
                                    (new Label(__('Display content in an iframe'), Label::OUTSIDE_LABEL_AFTER))->for('iframe')->class('classic'),
                                ]),
                        ]),

                    (new Div())
                    ->class(['two-boxes', 'even'])
                    ->items([
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('current'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['current'])
                                    ->label((new Label(__('Current text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('previous'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['previous'])
                                    ->label((new Label(__('Previous text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('next'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['next'])
                                    ->label((new Label(__('Next text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('close'))
                                    ->size(30)
                                    ->maxlength(255)
                                    ->value($as['close'])
                                    ->label((new Label(__('Close text'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                    ]),
                ]),
                (new Fieldset())
                ->legend((new Legend(__('Dimensions'))))
                ->fields([
                    (new Div())
                        ->class(['two-boxes', 'odd'])
                        ->items([
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('width'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['width'])
                                ->label((new Label(__('Fixed width'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('height'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['height'])
                                ->label((new Label(__('Fixed height'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('innerWidth'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['innerWidth'])
                                ->label((new Label(__('Fixed inner width'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('innerHeight'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['innerHeight'])
                                ->label((new Label(__('Fixed inner height'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Checkbox('scalePhotos', (bool) $as['scalePhotos'])),
                                (new Label(__('Scale photos'), Label::OUTSIDE_LABEL_AFTER))->for('scalePhotos')->class('classic'),
                            ]),
                            (new Para())
                                ->class('classic')
                                ->items([
                                    (new Checkbox('scrolling', (bool) $as['scrolling'])),
                                    (new Label(__('Show overflowing content'), Label::OUTSIDE_LABEL_AFTER))->for('scrolling')->class('classic'),
                                ]),
                        ]),

                    (new Div())
                    ->class(['two-boxes', 'even'])
                    ->items([
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('initialWidth'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['initialWidth'])
                                ->label((new Label(__('Initial width'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('initialHeight'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['initialHeight'])
                                ->label((new Label(__('Initial height'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('maxWidth'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['maxWidth'])
                                ->label((new Label(__('Max width'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                        ->class('classic')
                        ->items([
                            (new Input('maxHeight'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['maxHeight'])
                                ->label((new Label(__('Max height'), Label::OUTSIDE_TEXT_BEFORE))),
                        ]),
                    ]),
                ]),
                (new Fieldset())
                ->legend((new Legend(__('Javascript'))))
                ->fields([
                    (new Div())
                        ->class(['two-boxes', 'odd'])
                        ->items([
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('onOpen'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['onOpen'])
                                ->label((new Label(__('onOpen callback'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('onLoad'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['onLoad'])
                                ->label((new Label(__('onLoad callback'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                            (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('onComplete'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['onComplete'])
                                ->label((new Label(__('onComplete callback'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),

                        ]),

                    (new Div())
                    ->class(['two-boxes', 'even'])
                    ->items([
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('onCleanup'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['onCleanup'])
                                ->label((new Label(__('onCleanup callback'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),
                        (new Para())
                            ->class('classic')
                            ->items([
                                (new Input('onClosed'))
                                ->size(30)
                                ->maxlength(255)
                                ->value($as['onClosed'])
                                ->label((new Label(__('onClosed callback'), Label::OUTSIDE_TEXT_BEFORE))),
                            ]),

                    ]),
                ]),

                (new Hidden(['type'], 'advanced')),
                (new Input('save'))
                        ->type('submit')
                        ->value(__('Save configuration')),
                App::nonce()->formNonce(),
            ]),

        ])
        ->render();

        App::backend()->page()->helpBlock('colorbox');
        App::backend()->page()->closeModule();
    }
}
