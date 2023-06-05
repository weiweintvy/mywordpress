<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Load text domain for translations
 */
add_action( 'init', 'cf7msm_load_textdomain' );
function cf7msm_load_textdomain()
{
    load_plugin_textdomain( 'contact-form-7-multi-step-module', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Print a warning if cf7 not installed or activated.
 */
function contact_form_7_form_codes()
{
    global  $pagenow ;
    if ( $pagenow != 'plugins.php' ) {
        return;
    }
    if ( defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, CF7MSM_MIN_CF7_VERSION ) >= 0 ) {
        return;
    }
    add_action( 'admin_notices', 'cfformfieldserror' );
    function cfformfieldserror()
    {
        wp_enqueue_script( 'thickbox' );
        $out = '<div class="error" id="messages"><p>';
        
        if ( defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, CF7MSM_MIN_CF7_VERSION ) < 0 ) {
            $out .= sprintf( __( 'Please update the Contact Form 7 plugin.  Contact Form 7 Multi-Step Form plugin requires Contact Form 7 version %s or above.', 'contact-form-7-multi-step-module' ), CF7MSM_MIN_CF7_VERSION );
        } else {
            
            if ( file_exists( WP_PLUGIN_DIR . '/contact-form-7/wp-contact-form-7.php' ) ) {
                $out .= __( 'The Contact Form 7 plugin is installed, but <strong>you must activate Contact Form 7</strong> below for the Contact Form 7 Multi-Step Form to work.', 'contact-form-7-multi-step-module' );
            } else {
                $out .= sprintf( __( 'The Contact Form 7 plugin must be installed for the Contact Form 7 Multi-Step Form to work. <a href="%s" class="thickbox" title="Contact Form 7">Install Now.</a>', 'contact-form-7-multi-step-module' ), admin_url( 'plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550' ) );
            }
        
        }
        
        $out .= '</p></div>';
        echo  cf7msm_kses( $out ) ;
    }

}

add_action( 'plugins_loaded', 'contact_form_7_form_codes', 10 );
/**
 * Allow a set of default html tags
 */
function cf7msm_kses( $string, $additional_html = array() )
{
    $allowed_html = array_merge( array(
        'a'      => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
        'class'  => array(),
    ),
        'div'    => array(
        'class' => array(),
        'id'    => array(),
    ),
        'button' => array(
        'class' => array(),
        'id'    => array(),
    ),
        'p'      => array(),
        'br'     => array(),
        'em'     => array(),
        'strong' => array(),
    ), $additional_html );
    return wp_kses( $string, $allowed_html );
}

/**
 * Return the url with the plugin url prepended.
 */
function cf7msm_url( $path )
{
    return plugins_url( $path, CF7MSM_PLUGIN );
}

/**
 * init_sessions()
 *
 * @uses session_id()
 * @uses session_start()
 */
function cf7msm_init_sessions()
{
    if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
        // this is not needed during cron.
        return;
    }
    //try to set cookie
    
    if ( empty($_COOKIE['cf7msm_check']) ) {
        $force_session = apply_filters( 'cf7msm_force_session', false );
        $allow_session = apply_filters( 'cf7msm_allow_session', $force_session );
        
        if ( $allow_session ) {
            if ( !$force_session ) {
                setcookie(
                    'cf7msm_check',
                    1,
                    0,
                    COOKIEPATH,
                    COOKIE_DOMAIN
                );
            }
            if ( !session_id() ) {
                session_start();
            }
        }
    
    }

}

add_action( 'init', 'cf7msm_init_sessions' );
/**
 * Add scripts
 */
function cf7msm_scripts()
{
    wp_enqueue_style(
        'cf7msm_styles',
        plugins_url( '/resources/cf7msm.css', CF7MSM_PLUGIN ),
        array( 'contact-form-7' ),
        CF7MSM_VERSION
    );
    wp_enqueue_script(
        'cf7msm',
        plugins_url( '/resources/cf7msm.min.js', CF7MSM_PLUGIN ),
        array( 'jquery', 'contact-form-7' ),
        CF7MSM_VERSION,
        true
    );
    $cf7msm_posted_data = cf7msm_get( 'cf7msm_posted_data' );
    if ( empty($cf7msm_posted_data) ) {
        $cf7msm_posted_data = array();
    }
    wp_localize_script( 'cf7msm', 'cf7msm_posted_data', $cf7msm_posted_data );
}

add_action( 'wp_enqueue_scripts', 'cf7msm_scripts' );
/**
 *  Saves a variable to cookies or if not enabled, to session.
 */
function cf7msm_set( $var_name, $var_value )
{
    $force_session = apply_filters( 'cf7msm_force_session', false );
    $allow_session = apply_filters( 'cf7msm_allow_session', $force_session );
    $var_value = wp_unslash( $var_value );
    
    if ( $allow_session && empty($_COOKIE['cf7msm_check']) ) {
        $_SESSION[$var_name] = $var_value;
    } else {
        $json_encoded = '';
        //for php < 5.4
        
        if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
            $json_encoded = json_encode( $var_value, JSON_UNESCAPED_UNICODE );
        } else {
            $json_encoded = json_encode( $var_value );
        }
        
        setcookie(
            $var_name,
            $json_encoded,
            0,
            COOKIEPATH,
            COOKIE_DOMAIN
        );
    }

}

/**
 *  Get a variable from cookies or if not enabled, from session.
 */
function cf7msm_get( $var_name, $default = '' )
{
    $ret = $default;
    $force_session = apply_filters( 'cf7msm_force_session', false );
    $allow_session = apply_filters( 'cf7msm_allow_session', $force_session );
    
    if ( $allow_session && empty($_COOKIE['cf7msm_check']) ) {
        $ret = ( isset( $_SESSION[$var_name] ) ? cf7msm_sanitize_posted_data( $_SESSION[$var_name] ) : $default );
    } else {
        $ret = ( isset( $_COOKIE[$var_name] ) ? cf7msm_sanitize_posted_data( $_COOKIE[$var_name] ) : $default );
        $ret = json_decode( wp_unslash( $ret ), true );
    }
    
    // Conditional Fields plugin throws 500 error when these aren't set.
    
    if ( $var_name == 'cf7msm_posted_data' && class_exists( 'CF7CF' ) && method_exists( 'CF7CF', 'cf7msm_merge_post_with_cookie' ) ) {
        if ( !isset( $ret['_wpcf7cf_hidden_group_fields'] ) ) {
            $ret['_wpcf7cf_hidden_group_fields'] = '[]';
        }
        if ( !isset( $ret['_wpcf7cf_hidden_groups'] ) ) {
            $ret['_wpcf7cf_hidden_groups'] = '[]';
        }
        if ( !isset( $ret['_wpcf7cf_visible_groups'] ) ) {
            $ret['_wpcf7cf_visible_groups'] = '[]';
        }
    }
    
    return $ret;
}

/**
 * Remove a saved variable.
 */
function cf7msm_remove( $var_name )
{
    $ret = '';
    $force_session = apply_filters( 'cf7msm_force_session', false );
    $allow_session = apply_filters( 'cf7msm_allow_session', $force_session );
    
    if ( $allow_session && empty($_COOKIE['cf7msm_check']) ) {
        if ( isset( $_SESSION[$var_name] ) ) {
            unset( $_SESSION[$var_name] );
        }
    } else {
        if ( isset( $_COOKIE[$var_name] ) ) {
            setcookie(
                $var_name,
                '',
                1,
                COOKIEPATH,
                COOKIE_DOMAIN
            );
        }
    }

}

/**
 * Hide the second step of a form.  looks at hidden field 'step'.
 * Always show if the form is the first step.
 * If it's not the first step, make sure it's the next form in the steps.
 */
function cf7msm_step_2( $cf7 )
{
    $has_wpcf7_class = class_exists( 'WPCF7_ContactForm' ) && method_exists( $cf7, 'prop' );
    $form_id = '';
    $using_new = false;
    $is_invalid = false;
    
    if ( $has_wpcf7_class ) {
        $formstring = $cf7->prop( 'form' );
        $form_id = $cf7->id();
    } else {
        $formstring = $cf7->form;
        $form_id = $cf7->id;
    }
    
    
    if ( !is_admin() ) {
        $tags = $cf7->scan_form_tags( array(
            'type' => array( 'multistep' ),
        ) );
        
        if ( !empty($tags) ) {
            // is a cf7msm form
            $is_first_step = false;
            foreach ( $tags as $tag ) {
                if ( empty($tag->name) ) {
                    continue;
                }
                $using_new = true;
                $options = $tag->options;
                
                if ( !empty($options) && in_array( 'first_step', $options ) ) {
                    $is_first_step = true;
                    break;
                }
            
            }
            
            if ( $using_new ) {
                $did_first_step = cf7msm_get( 'cf7msm-first-step' );
                if ( !$is_first_step && empty($did_first_step) ) {
                    $is_invalid = true;
                }
            }
        
        }
    
    }
    
    //old way - check if form has a step field
    
    if ( !is_admin() && (preg_match( '/\\[multistep "(\\d+)-(\\d+)-?(.*)"\\]/', $formstring, $matches ) || preg_match( '/\\[hidden cf7msm-step "(\\d+)-(\\d+)"\\]/', $formstring, $matches )) ) {
        // don't support using both new and old format
        
        if ( $using_new ) {
            
            if ( $has_wpcf7_class ) {
                $cf7->set_properties( array(
                    'form' => apply_filters( 'cf7msm_error_invalid_format', __( 'Error: This form is using two different formats of the multistep tag.' ), $form_id ),
                ) );
            } else {
                $cf7->form = apply_filters( 'cf7msm_error_invalid_format', __( 'Error: This form is using two different formats of the multistep tag.' ), $form_id );
            }
            
            return $cf7;
        }
        
        // TODO:  pull this out of this if statement when implementing new tag's step attribute.
        $step = cf7msm_get( 'cf7msm-step' );
        $missing_prev_step = (int) $step + 1 < $matches[1];
        
        if ( $missing_prev_step ) {
            $prev_urls = cf7msm_get( 'cf7msm_prev_urls' );
            if ( !empty($prev_urls) ) {
                foreach ( $prev_urls as $step_string => $url ) {
                    $step_parts = explode( '-', $step_string );
                    if ( !empty($step_parts) ) {
                        
                        if ( (int) $step_parts[0] >= $matches[1] ) {
                            $missing_prev_step = false;
                            break;
                        }
                    
                    }
                }
            }
        }
        
        if ( !isset( $matches[1] ) || $matches[1] != 1 && empty($step) || $matches[1] != 1 && $missing_prev_step ) {
            $is_invalid = true;
        }
    }
    
    if ( $is_invalid ) {
        
        if ( $has_wpcf7_class ) {
            $cf7->set_properties( array(
                'form' => apply_filters( 'wh_hide_cf7_step_message', $cf7->message( 'invalid_first_step' ), $form_id ),
            ) );
        } else {
            $cf7->form = apply_filters( 'wh_hide_cf7_step_message', $cf7->message( 'invalid_first_step' ), $form_id );
        }
    
    }
    return $cf7;
}

add_action( 'wpcf7_contact_form', 'cf7msm_step_2' );
/**
 * Handle a multi-step cf7 form for cf7 3.9+
 */
function cf7msm_add_other_steps_filter( $cf7_posted_data )
{
    $curr_step = '';
    $last_step = '';
    $is_last_step = false;
    if ( empty($cf7_posted_data['cf7msm-step']) && empty($cf7_posted_data['cf7msm_options']) ) {
        return $cf7_posted_data;
    }
    if ( isset( $cf7_posted_data['cf7msm-step'] ) ) {
        
        if ( preg_match( '/(\\d+)-(\\d+)/', $cf7_posted_data['cf7msm-step'], $matches ) ) {
            $curr_step = $matches[1];
            $last_step = $matches[2];
            $is_last_step = $curr_step == $last_step;
        }
    
    }
    
    if ( !empty($curr_step) && !empty($last_step) ) {
        //for step-restricted back button
        $prev_urls = cf7msm_get( 'cf7msm_prev_urls' );
        if ( empty($prev_urls) ) {
            $prev_urls = array();
        }
        //old example:
        // on step 1,
        //    prev url {"2-3":"page-2"} will be set.
        //    back button is pressed, key "1-3" is looked up and returns undefined
        // on step 2,
        //    prev url {"3-3":"page-2"} will be set.
        //    back button is pressed, key "2-3" is looked up and returns "page-1"
        // on step 3,
        //    prev url {"4-3":"page-3"} will be set. - not used
        //    back button is pressed, key "3-3" is looked up and returns "page-2"
        // step
        $prev_urls[$curr_step + 1 . '-' . $last_step] = cf7msm_current_url();
        cf7msm_set( 'cf7msm_prev_urls', $prev_urls );
    }
    
    // TODO:  set curr_step and last_step for new tag when implemented.
    
    if ( !empty($cf7_posted_data['cf7msm_options']) ) {
        $options = json_decode( stripslashes( $cf7_posted_data['cf7msm_options'] ), true );
        $is_last_step = !empty($options['last_step']);
    }
    
    $use_cookies = true;
    
    if ( !empty($cf7_posted_data['cf7msm-no-ss']) || $use_cookies ) {
        $prev_data = cf7msm_get( 'cf7msm_posted_data', '' );
        if ( !is_array( $prev_data ) ) {
            $prev_data = array();
        }
        //remove empty [form] tags from posted_data so $prev_data can be stored.
        $fes = wpcf7_scan_form_tags();
        foreach ( $fes as $fe ) {
            if ( empty($fe['name']) || $fe['type'] != 'form' && $fe['type'] != 'multiform' ) {
                continue;
            }
            unset( $cf7_posted_data[$fe['name']] );
        }
        $free_text_keys = array();
        foreach ( $prev_data as $key => $value ) {
            
            if ( strpos( $key, CF7MSM_FREE_TEXT_PREFIX_RADIO ) === 0 ) {
                $free_text_keys[$key] = str_replace( CF7MSM_FREE_TEXT_PREFIX_RADIO, '', $key );
            } else {
                if ( strpos( $key, CF7MSM_FREE_TEXT_PREFIX_CHECKBOX ) === 0 ) {
                    $free_text_keys[$key] = str_replace( CF7MSM_FREE_TEXT_PREFIX_CHECKBOX, '', $key );
                }
            }
        
        }
        //if original key is set and not free text, remove free text to reflect posted data.
        foreach ( $free_text_keys as $free_text_key => $original_key ) {
            if ( isset( $cf7_posted_data[$original_key] ) && !isset( $cf7_posted_data[$free_text_key] ) ) {
                unset( $prev_data[$free_text_key] );
            }
        }
        $cf7_posted_data = array_merge( $prev_data, $cf7_posted_data );
    }
    
    return $cf7_posted_data;
}

add_filter( 'wpcf7_posted_data', 'cf7msm_add_other_steps_filter', 9 );
/**
 * If use cookies and not the last step, store the values here after it's been validated.
 */
function cf7msm_store_data_steps()
{
    $use_cookies = true;
    
    if ( $use_cookies ) {
        $cf7_posted_data = WPCF7_Submission::get_instance()->get_posted_data();
        $is_last_step = false;
        if ( empty($cf7_posted_data['cf7msm-step']) && empty($cf7_posted_data['cf7msm_options']) ) {
            return;
        }
        
        if ( !empty($cf7_posted_data['cf7msm_options']) ) {
            $options = json_decode( stripslashes( $cf7_posted_data['cf7msm_options'] ), true );
            $is_last_step = !empty($options['last_step']);
        }
        
        
        if ( !$is_last_step ) {
            // don't track big cookies on last step bc submitting on the last step doesn't use the cookie.
            cf7msm_track_big_cookie( $cf7_posted_data );
            cf7msm_set( 'cf7msm_posted_data', $cf7_posted_data );
        }
    
    }

}

add_action( 'wpcf7_before_send_mail', 'cf7msm_store_data_steps' );
/**
 * Skip sending the mail if this is a multi step form and not the last step.
 */
function cf7msm_skip_send_mail( $skip_mail, $wpcf7 )
{
    $posted_data = WPCF7_Submission::get_instance()->get_posted_data();
    
    if ( !empty($posted_data['cf7msm_options']) ) {
        $skip_mail = true;
        $options = json_decode( stripslashes( $posted_data['cf7msm_options'] ), true );
        if ( !empty($options['send_email']) ) {
            $skip_mail = false;
        }
    } else {
        $step_string = parse_form_for_multistep( $wpcf7, true );
        
        if ( !empty($step_string) ) {
            $steps = explode( '-', $step_string );
            $curr_step = $steps[0];
            $last_step = $steps[1];
            if ( $curr_step != $last_step ) {
                $skip_mail = true;
            }
        }
    
    }
    
    return $skip_mail;
}

add_filter(
    'wpcf7_skip_mail',
    'cf7msm_skip_send_mail',
    10,
    2
);
/**
 * Sets the current step if valid.
 */
function cf7msm_set_step( $result, $tags )
{
    $posted_data = WPCF7_Submission::get_instance()->get_posted_data();
    
    if ( !empty($posted_data['cf7msm-step']) ) {
        $step = $posted_data['cf7msm-step'];
        
        if ( preg_match( '/(\\d+)-(\\d+)/', $step, $matches ) ) {
            $curr_step = $matches[1];
            $last_step = $matches[2];
            
            if ( $result->is_valid() ) {
                if ( $curr_step != $last_step ) {
                    cf7msm_set( 'cf7msm-step', $curr_step );
                }
            } else {
                $stored_step = cf7msm_get( 'cf7msm-step' );
                if ( $stored_step >= $curr_step ) {
                    //reduce it so user cannot move onto next step.
                    cf7msm_set( 'cf7msm-step', intval( $curr_step ) - 1 );
                }
            }
        
        }
    
    } else {
        
        if ( !empty($posted_data['cf7msm_options']) ) {
            $options = json_decode( stripslashes( $posted_data['cf7msm_options'] ), true );
            if ( !empty($options['first_step']) ) {
                cf7msm_set( 'cf7msm-first-step', 1 );
            }
        }
    
    }
    
    return $result;
}

add_filter(
    'wpcf7_validate',
    'cf7msm_set_step',
    99,
    2
);
/**
 * Clean things up after mail has been sent.
 */
function cf7msm_mail_sent()
{
    $posted_data = WPCF7_Submission::get_instance()->get_posted_data();
    $wpcf7 = WPCF7_ContactForm::get_current();
    $is_last_step = false;
    $no_ajax_redirect_url = '';
    
    if ( isset( $posted_data['cf7msm_options'] ) ) {
        $options = json_decode( stripslashes( $posted_data['cf7msm_options'] ), true );
        if ( !empty($options['last_step']) ) {
            $is_last_step = true;
        }
        if ( !empty($options['next_url']) ) {
            $no_ajax_redirect_url = $options['next_url'];
        }
    } else {
        
        if ( isset( $posted_data['cf7msm-step'] ) ) {
            // old way
            $curr_step = '';
            $step = $posted_data['cf7msm-step'];
            
            if ( preg_match( '/(\\d+)-(\\d+)/', $step, $matches ) ) {
                $curr_step = $matches[1];
                $last_step = $matches[2];
            }
            
            
            if ( $curr_step == $last_step ) {
                $is_last_step = true;
            } else {
                $formstring = $wpcf7->prop( 'form' );
                // redirect when ajax is disabled and not doing rest and not doing ajax
                if ( !(defined( 'REST_REQUEST' ) && REST_REQUEST) && !(defined( 'DOING_AJAX' ) && DOING_AJAX) ) {
                    //get url from saved form, not $_POST.  be safe.
                    $no_ajax_redirect_url = parse_form_for_multistep( $wpcf7 );
                }
            }
        
        }
    
    }
    
    
    if ( $is_last_step ) {
        // while the cookie could have already been cut off, I don't want to falsely trigger
        // if cookies just plainly don't save. so just test on the last submission
        cf7msm_maybe_set_big_cookie_notice();
        $stats = get_option( '_cf7msm_stats', array() );
        $count = ( !empty($stats['count']) ? $stats['count'] : 0 );
        $stats['count'] = 1 + $count;
        update_option( '_cf7msm_stats', $stats );
        cf7msm_remove( 'cf7msm-step' );
        cf7msm_remove( 'cf7msm_posted_data' );
        cf7msm_remove( 'cf7msm_prev_urls' );
        cf7msm_remove( 'cf7msm-first-step' );
        cf7msm_remove( 'cf7msm_big_cookie' );
    }
    
    // redirect when ajax is disabled and not doing rest and not doing ajax
    
    if ( !(defined( 'REST_REQUEST' ) && REST_REQUEST) && !(defined( 'DOING_AJAX' ) && DOING_AJAX) ) {
        
        if ( empty($no_ajax_redirect_url) ) {
            // if using old additional_settings way
            $subject = $wpcf7->prop( 'additional_settings' );
            $pattern = '/location\\.replace\\(\'([^\']*)\'\\);/';
            preg_match( $pattern, $subject, $matches );
            if ( count( $matches ) == 2 ) {
                $no_ajax_redirect_url = $matches[1];
            }
        }
        
        $no_ajax_redirect_url = apply_filters( 'cf7msm_redirect_url', $no_ajax_redirect_url, $wpcf7->id() );
        
        if ( !empty($no_ajax_redirect_url) ) {
            wp_redirect( esc_url( $no_ajax_redirect_url ) );
            exit;
        }
    
    }

}

add_action( 'wpcf7_mail_sent', 'cf7msm_mail_sent' );
/**
 * Go through a wpcf7 form's formstring and find the multistep url.
 * If $steps is true, return the steps part as "<curr>-<total>", otherwise return the url.
 */
function parse_form_for_multistep( $wpcf7, $steps = false )
{
    $formstring = $wpcf7->prop( 'form' );
    if ( preg_match( '/\\[multistep "(\\d+)-(\\d+)-(.+)"\\]/', $formstring, $matches ) ) {
        
        if ( $steps ) {
            if ( !empty($matches[1]) && !empty($matches[2]) ) {
                return $matches[1] . '-' . $matches[2];
            }
        } else {
            if ( !empty($matches[3]) ) {
                return $matches[3];
            }
        }
    
    }
    
    if ( !$steps ) {
        // new way
        $tags = $wpcf7->scan_form_tags( array(
            'type' => array( 'multistep' ),
        ) );
        
        if ( !empty($tags) ) {
            // is a cf7msm form
            $is_first_step = true;
            $has_new_version = false;
            // return last one first.
            $tags = array_reverse( $tags );
            foreach ( $tags as $tag ) {
                if ( empty($tag->name) ) {
                    continue;
                }
                if ( !empty($tag->values) ) {
                    return (string) reset( $tag->values );
                }
            }
        }
    
    }
    
    return '';
}

/**
 * return the full url.
 */
function cf7msm_current_url()
{
    $page_url = 'http';
    if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
        $page_url .= "s";
    }
    $page_url .= "://";
    
    if ( isset( $_SERVER["SERVER_PORT"] ) && $_SERVER["SERVER_PORT"] != "80" ) {
        $page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    
    return esc_url( $page_url );
}

/**
 * 
 */
function cf7msm_setup_next_url( $not_used )
{
    global  $cf7msm_redirect_urls ;
    if ( empty($cf7msm_redirect_urls) ) {
        $cf7msm_redirect_urls = array();
    }
    $wpcf7 = WPCF7_ContactForm::get_current();
    $redirect_url = parse_form_for_multistep( $wpcf7 );
    $redirect_url = apply_filters( 'cf7msm_redirect_url', $redirect_url, $wpcf7->id() );
    if ( !empty($redirect_url) ) {
        $cf7msm_redirect_urls[$wpcf7->id()] = $redirect_url;
    }
    wp_localize_script( 'cf7msm', 'cf7msm_redirect_urls', $cf7msm_redirect_urls );
    return $not_used;
}

add_filter( 'wpcf7_form_action_url', 'cf7msm_setup_next_url' );
/**
 * Skip saving to DB if skip_save is set.
 */
function cf7msm_skip_save( $form, $results )
{
    $posted_data = WPCF7_Submission::get_instance()->get_posted_data();
    
    if ( !empty($posted_data['cf7msm_options']) ) {
        $options = json_decode( stripslashes( $posted_data['cf7msm_options'] ), true );
        
        if ( !empty($options['skip_save']) ) {
            remove_action(
                'wpcf7_submit',
                'wpcf7_flamingo_submit',
                10,
                2
            );
            remove_action( 'wpcf7_before_send_mail', 'cfdb7_before_send_mail' );
            remove_action( 'wpcf7_before_send_mail', 'vsz_cf7_before_send_email' );
        }
    
    }

}

add_action(
    'wpcf7_before_send_mail',
    'cf7msm_skip_save',
    9,
    2
);
/**
 * Track big cookies
 */
function cf7msm_track_big_cookie( $posted_data )
{
    // -1 if cookie is not set.
    $has_big_cookie = cf7msm_get( 'cf7msm_big_cookie', -1 );
    
    if ( cf7msm_cookie_size( $posted_data ) >= CF7MSM_COOKIE_SIZE_THRESHOLD ) {
        cf7msm_set( 'cf7msm_big_cookie', 1 );
    } else {
        if ( $has_big_cookie == -1 ) {
            cf7msm_set( 'cf7msm_big_cookie', 0 );
        }
    }

}

/**
 * Since posted data can be truncated after setting it to a cookie, 
 * get size of the actual posted_data and add then the cookie.
 */
function cf7msm_cookie_size( $posted_data = array() )
{
    $size = 0;
    foreach ( $posted_data as $key => $value ) {
        $size += mb_strlen( $key, '8bit' );
        $size += mb_strlen( json_encode( $value, JSON_UNESCAPED_UNICODE ), '8bit' );
    }
    if ( !empty($_COOKIE) ) {
        foreach ( $_COOKIE as $key => $value ) {
            if ( $key == 'cf7msm_posted_data' ) {
                continue;
            }
            $size += mb_strlen( $key, '8bit' );
            $size += mb_strlen( $value, '8bit' );
        }
    }
    return $size;
}

/**
 * Set the notice of a big cookie.
 */
function cf7msm_maybe_set_big_cookie_notice()
{
    if ( cf7msm_fs()->can_use_premium_code() ) {
        return;
    }
    $stats = get_option( '_cf7msm_stats', array() );
    $notice = ( !empty($stats['notice-big-cookie']) ? $stats['notice-big-cookie'] : '' );
    $has_big_cookie = cf7msm_get( 'cf7msm_big_cookie', -1 );
    
    if ( $notice !== 'no' && $has_big_cookie == 1 ) {
        if ( empty($stats['big_cookies']) ) {
            $stats['big_cookies'] = 0;
        }
        $stats['big_cookies'] += 1;
        update_option( '_cf7msm_stats', $stats );
    }

}

/**
 * from cf7 submission.php
 */
function cf7msm_sanitize_posted_data( $value )
{
    
    if ( is_array( $value ) ) {
        $value = array_map( 'cf7msm_sanitize_posted_data', $value );
    } elseif ( is_string( $value ) ) {
        $value = wp_check_invalid_utf8( $value );
        $value = wp_kses_no_null( $value );
    }
    
    return $value;
}
