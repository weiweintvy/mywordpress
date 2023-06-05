<?php

/*
Plugin Name: Contact Form 7 Multi-Step Forms
Plugin URI: http://www.mymonkeydo.com/contact-form-7-multi-step-module/
Description: Enables the Contact Form 7 plugin to create multi-page, multi-step forms.
Author: Webhead LLC.
Author URI: http://webheadcoder.com
Version: 4.2.1
Text Domain: contact-form-7-multi-step-module
*/
/*  Copyright 2021 Webhead LLC (email: info at webheadcoder.com)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'cf7msm_fs' ) ) {
    cf7msm_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'cf7msm_fs' ) ) {
        // Create a helper function for easy SDK access.
        function cf7msm_fs()
        {
            global  $cf7msm_fs ;
            
            if ( !isset( $cf7msm_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $cf7msm_fs = fs_dynamic_init( array(
                    'id'             => '1614',
                    'slug'           => 'contact-form-7-multi-step-module',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_b445061ad8b540f6a89c2c4f4df19',
                    'is_premium'     => false,
                    'premium_suffix' => '(Pro)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'first-path' => 'plugins.php',
                    'contact'    => false,
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $cf7msm_fs;
        }
        
        // Init Freemius.
        cf7msm_fs();
        // Signal that SDK was initiated.
        do_action( 'cf7msm_fs_loaded' );
        define( 'CF7MSM_VERSION', '4.2.1' );
        define( 'CF7MSM_PLUGIN', __FILE__ );
        define( 'CF7MSM_FREE_TEXT_PREFIX_RADIO', '_wpcf7_radio_free_text_' );
        define( 'CF7MSM_FREE_TEXT_PREFIX_CHECKBOX', '_wpcf7_checkbox_free_text_' );
        define( 'CF7MSM_MIN_CF7_VERSION', '4.8' );
        define( 'CF7MSM_LEARN_MORE_URL', 'https://webheadcoder.com/contact-form-7-multi-step-forms/#pro' );
        define( 'CF7MSM_COOKIE_SIZE_THRESHOLD', 3684 );
        //4093 * 90%
        /**
         * Change update message
         */
        function cf7msm_fs_custom_connect_message_on_update(
            $message,
            $user_first_name,
            $plugin_title,
            $user_login,
            $site_link,
            $freemius_link
        )
        {
            $limited_time = '';
            return cf7msm_kses( sprintf( __( 'Please help improve the %1$s plugin!  I have chosen to use %2$s to get an idea of how users use my plugin.<br><br>  If you opt-in, the administrator email and some data about your usage of %1$s will be sent to %2$s. If you skip this, that\'s okay! The plugin will still work just fine.', 'contact-form-7-multi-step-module' ), '<strong>' . $plugin_title . '</strong>', $freemius_link ) ) . $limited_time;
        }
        
        cf7msm_fs()->add_filter(
            'connect_message_on_update',
            'cf7msm_fs_custom_connect_message_on_update',
            10,
            6
        );
        /**
         * Add account link if paying.
         */
        function cf7msm_plugin_action_links( $links )
        {
            if ( !is_array( $links ) ) {
                $links = array();
            }
            if ( cf7msm_fs()->is_not_paying() ) {
                $links[] = '<a href="' . CF7MSM_LEARN_MORE_URL . '" target="_blank">' . __( 'Learn about PRO' ) . '</a>';
            }
            return $links;
        }
        
        add_filter( "plugin_action_links_" . plugin_basename( CF7MSM_PLUGIN ), 'cf7msm_plugin_action_links' );
        /**
         * Run on activation
         */
        function cf7msm_activation()
        {
            $stats = get_option( '_cf7msm_stats', array() );
            $install_date = ( !empty($stats['install_date']) ? $stats['install_date'] : 0 );
            
            if ( empty($install_date) ) {
                $stats['install_date'] = time();
                update_option( '_cf7msm_stats', $stats );
            }
            
            update_option( '_cf7msm_version', CF7MSM_VERSION );
        }
        
        register_activation_hook( CF7MSM_PLUGIN, 'cf7msm_activation' );
        /**
         * Check if everything is up to date.
         */
        function cf7msm_plugin_check()
        {
            $version = get_option( '_cf7msm_version', '' );
            if ( $version !== CF7MSM_PLUGIN ) {
                cf7msm_activation();
            }
        }
        
        add_action( 'plugins_loaded', 'cf7msm_plugin_check' );
        /**
         * Run on deactivation
         */
        function cf7msm_deactivation()
        {
            delete_option( '_cf7msm_stats' );
            delete_option( '_cf7msm_version' );
        }
        
        register_deactivation_hook( CF7MSM_PLUGIN, 'cf7msm_deactivation' );
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'cf7msm.php';
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'cf7msm-admin.php';
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/common.php';
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-multistep.php';
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-session.php';
        require_once plugin_dir_path( CF7MSM_PLUGIN ) . 'form-tags/module-back.php';
    }

}
