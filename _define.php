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

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Colorbox',									// Name
    'A lightweight customizable lightbox',		// Description
    'Philippe aka amalgame and Tomtom',			// Author
    '3.4.1',                   					// Version
    [
        'requires' => [['core', '2.23']],   	// Dependencies
        'permissions' => 'usage,contentadmin', 	// Permissions
        'type' => 'plugin',             	    // Type
        'priority' => 2000                 	    // Priority
    ]
);
