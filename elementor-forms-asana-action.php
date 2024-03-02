<?php

/**
 * Plugin Name: Elementor Forms Asana Action
 * Description: Custom addon which will send an Elementor Pro form to asana
 * Plugin URI:  https://www.thebytewizards.com/
 * Version:     1.0.0
 * Author:      The Byte Wizards
 * Author URI:  https://www.thebytewizards.com/
 * Text Domain: tbw-elementor-asana
 *
 * Elementor tested up to: 3.19.4
 * Elementor Pro tested up to: 3.19.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add new form action after form submission.
 *
 * @since 1.0.0
 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 * @return void
 */
function add_new_asana_action($form_actions_registrar)
{

    include_once(__DIR__ . '/form-actions/asana.php');

    $form_actions_registrar->register(new \TBW_Asana_Action_After_Submit());

}
add_action('elementor_pro/forms/actions/register', 'add_new_asana_action');
