<?php

/*
Plugin Name: Better jQuery Migrate Helper
Plugin URI: http://www.wpadami.com/
Description: Revert jQuery to 1.12.4-wp and jQuery Migrate to 1.4.1-wp
Version: 1.0
Author: kaisercrazy
Author URI: http://www.wpadami.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class BetterjQueryMigrateHelper
{
    private $better_jquery_migrate_helper_options;

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'better_jquery_migrate_helper_add_plugin_page' ));
        add_action('admin_init', array( $this, 'better_jquery_migrate_helper_page_init' ));
        add_action('wp_enqueue_scripts', array($this,'replace_core_jquery_version'));
        add_action('admin_enqueue_scripts', array($this,'replace_core_jquery_version'));
    }

    public function better_jquery_migrate_helper_add_plugin_page()
    {
        add_options_page(
            'Better jQuery Migrate Helper', // page_title
            'Better jQuery Migrate Helper', // menu_title
            'manage_options', // capability
            'better-jquery-migrate-helper', // menu_slug
            array( $this, 'better_jquery_migrate_helper_admin_page' ) // function
        );
    }

    public function better_jquery_migrate_helper_admin_page()
    {
        $this->better_jquery_migrate_helper_options = get_option('better_jquery_migrate_helper_options_name'); ?>

		<div class="wrap">
			<h2>Better jQuery Migrate Helper</h2>
			<p></p>
				<?php settings_errors(); ?>

			<form method="post" action="options.php">
					<?php
                    settings_fields('better_jquery_migrate_helper_option_group');
        do_settings_sections('better-jquery-migrate-helper-admin');
        submit_button(); ?>
			</form>
		</div>
			<?php
    }

    public function better_jquery_migrate_helper_page_init()
    {
        register_setting(
            'better_jquery_migrate_helper_option_group', // option_group
            'better_jquery_migrate_helper_options_name', // option_name
            array( $this, 'better_jquery_migrate_helper_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'better_jquery_migrate_helper_settings_section', // id
            'Settings', // title
            array( $this, 'better_jquery_migrate_helper_section_info' ), // callback
            'better-jquery-migrate-helper-admin' // page
        );

        add_settings_field(
            'active_for_admin_area', // id
            'Active For Admin Area', // title
            array( $this, 'active_for_admin_area_checkbox' ), // callback
            'better-jquery-migrate-helper-admin', // page
            'better_jquery_migrate_helper_settings_section' // section
        );
        add_settings_field(
            'active_for_frontend_area', // id
            'Active For Template', // title
            array( $this, 'active_for_frontend_area_checkbox' ), // callback
            'better-jquery-migrate-helper-admin', // page
            'better_jquery_migrate_helper_settings_section' // section
        );
    }

    public function better_jquery_migrate_helper_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['active_for_admin_area'])) {
            $sanitary_values['active_for_admin_area'] = sanitize_text_field($input['active_for_admin_area']);
        }

        if (isset($input['active_for_frontend_area'])) {
            $sanitary_values['active_for_frontend_area'] = sanitize_text_field($input['active_for_frontend_area']);
        }

        return $sanitary_values;
    }

    public function better_jquery_migrate_helper_section_info()
    {
    }

    public function active_for_admin_area_checkbox()
    {
        echo '<input class="regular-text" type="checkbox" name="better_jquery_migrate_helper_options_name[active_for_admin_area]" id="active_for_admin_area" value="1" '.checked(1, $this->better_jquery_migrate_helper_options['active_for_admin_area'], false).'>';
    }

    public function active_for_frontend_area_checkbox()
    {
        echo '<input class="regular-text" type="checkbox" name="better_jquery_migrate_helper_options_name[active_for_frontend_area]" id="active_for_frontend_area" value="1" '.checked(1, $this->better_jquery_migrate_helper_options['active_for_frontend_area'], false).'>';
    }

    public function replace_core_jquery_version()
    {
        $this->better_jquery_migrate_helper_options = get_option('better_jquery_migrate_helper_options_name');
        if ($this->better_jquery_migrate_helper_options['active_for_admin_area'] && $this->better_jquery_migrate_helper_options['active_for_frontend_area']) {
            define('CONCATENATE_SCRIPTS', false);
            wp_deregister_script('jquery-core');
            wp_deregister_script('jquery-migrate');
            wp_register_script('jquery-core', plugin_dir_url(__FILE__)."js/jquery-1.12.4-wp.js", array(), null);
            wp_register_script('jquery-migrate', plugin_dir_url(__FILE__)."js/jquery-migrate-1.4.1-wp.js", array("jquery-core"));
            wp_enqueue_script('jquery-core');
            wp_enqueue_script('jquery-migrate');
        } elseif ($this->better_jquery_migrate_helper_options['active_for_admin_area'] && !$this->better_jquery_migrate_helper_options['active_for_frontend_area']) {
            if (is_admin()) {
                wp_deregister_script('jquery-core');
                wp_deregister_script('jquery-migrate');
                wp_register_script('jquery-core', plugin_dir_url(__FILE__)."js/jquery-1.12.4-wp.js", array(), null);
                wp_register_script('jquery-migrate', plugin_dir_url(__FILE__)."js/jquery-migrate-1.4.1-wp.js", array("jquery-core"));
                wp_enqueue_script('jquery-core');
                wp_enqueue_script('jquery-migrate');
            }
        } elseif (!$this->better_jquery_migrate_helper_options['active_for_admin_area'] && $this->better_jquery_migrate_helper_options['active_for_frontend_area']) {
            if (!is_admin()) {
                wp_deregister_script('jquery-core');
                wp_deregister_script('jquery-migrate');
                wp_register_script('jquery-core', plugin_dir_url(__FILE__)."js/jquery-1.12.4-wp.js", array(), null);
                wp_register_script('jquery-migrate', plugin_dir_url(__FILE__)."js/jquery-migrate-1.4.1-wp.js", array("jquery-core"));
                wp_enqueue_script('jquery-core');
                wp_enqueue_script('jquery-migrate');
            }
        } else {
            return;
        }
    }
}
$better_jquery_helper = new BetterjQueryMigrateHelper();
