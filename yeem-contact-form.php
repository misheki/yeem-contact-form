<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.michelleanneyee.com
 * @since             1.0.0
 * @package           Yeem_Contact_Form
 *
 * @wordpress-plugin
 * Plugin Name:       Yeem Contact Form
 * Plugin URI:        www.michelleanneyee.com/yeem-contact-form
 * Description:       An easy to use customizable contact form.
 * Version:           1.0.0
 * Author:            Michelle Anne Yee
 * Author URI:        www.michelleanneyee.com
 * Text Domain:       yeem-contact-form
 * Domain Path:       /languages
 * License: GPL v3

    Copyright 2017 YEEM CONTACT FORM (mishi@michelleanneyee.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

DEFINE("PLUGIN_BASENAME",plugin_basename( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yeem-contact-form-activator.php
 */
function activate_yeem_contact_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yeem-contact-form-activator.php';
	Yeem_Contact_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yeem-contact-form-deactivator.php
 */
function deactivate_yeem_contact_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yeem-contact-form-deactivator.php';
	Yeem_Contact_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yeem_contact_form' );
register_deactivation_hook( __FILE__, 'deactivate_yeem_contact_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yeem-contact-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yeem_contact_form() {

	$plugin = new Yeem_Contact_Form();
	$plugin->run();

}
run_yeem_contact_form();
