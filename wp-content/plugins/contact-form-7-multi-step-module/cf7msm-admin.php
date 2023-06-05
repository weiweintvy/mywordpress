<?php


/**
 * Tag generator helper scripts
 */
function cf7msm_admin_enqueue_scripts( $hook_suffix ) {
    $notice_big_cookie = cf7msm_maybe_display_notice_big_cookie();
    $notice_num = cf7msm_maybe_display_notice();
    if ( $notice_big_cookie || !empty( $notice_num ) ) {

        wp_enqueue_script( 'cf7msm-admin-notice',
            cf7msm_url( 'resources/cf7msm-notice.min.js' ),
            array( 'jquery' ),
            CF7MSM_VERSION, true );
        wp_enqueue_style( 'cf7msm-admin-notice',
            cf7msm_url( 'resources/cf7msm-notice.css' ),
            array(), CF7MSM_VERSION );

        wp_localize_script( 'cf7msm-admin-notice', 'cf7msm_admin', array(
            'nonce'         => wp_create_nonce( 'cf7msm-admin-nonce' )
        ));
    }

    if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
        return;
    }
    wp_enqueue_script( 'cf7msm-admin-taggenerator',
        cf7msm_url( 'form-tags/js/tag-generator.js' ),
        array( 'jquery' ), CF7MSM_VERSION, true );

    wp_enqueue_style( 'cf7msm-admin',
        cf7msm_url( 'form-tags/css/styles.css' ),
        array( 'contact-form-7-admin' ), CF7MSM_VERSION );

    if ( cf7msm_fs()->is_not_paying() ) {
        wp_enqueue_script( 'cf7msm-admin-checkout',
            'https://checkout.freemius.com/checkout.min.js',
            array( 'jquery' ),
            CF7MSM_VERSION, true );

        wp_enqueue_script( 'cf7msm-admin-panel',
            cf7msm_url( 'resources/cf7msm-admin.min.js' ),
            array( 'jquery', 'cf7msm-admin-checkout' ),
            CF7MSM_VERSION, true );
        wp_localize_script( 'cf7msm-admin-panel', 'cf7msm_admin_panel', array(
            'url'         => cf7msm_url('')
        ));

        wp_enqueue_style( 'cf7msm-admin-panel',
            cf7msm_url( 'resources/cf7msm-admin.css' ),
            array( 'contact-form-7-admin' ), CF7MSM_VERSION );

        $wpcf7 = WPCF7_ContactForm::get_current();

        if ( $wpcf7 ) {        
            $tags = $wpcf7->scan_form_tags( array( 
                'type' => array( 'multistep' ) 
            ) );

            if ( !empty( $tags ) ) {
                add_action( 'wpcf7_admin_footer', 'cf7msm_upgrade_panel' );   
            }
        }
    }

}
add_action( 'admin_enqueue_scripts', 'cf7msm_admin_enqueue_scripts' );

/**
 * Show upgrade information
 */
function cf7msm_upgrade_panel() {
?>
    <div id="upgradediv" class="postbox hide">
        <h3><?php echo esc_html( __( 'CF7 Multi-Step Forms', 'contact-form-7' ) ); ?></h3>
        <div class="inside">
            <?php echo cf7msm_kses( __( '<p>Not getting all information from your Multi-Step forms? </p><p>Consider upgrading to allow for longer multi-step forms.</p>' ) ); ?>
            <br>
            <div class="aligncenter">
                <?php printf( cf7msm_kses( 
                __( '<a href="#" class="cf7msm-freemius-purchase">Upgrade Now</a><br><a href="%1$s" target="_blank">Learn more</a><a href="%1$s" class="external dashicons dashicons-external" target="_blank"></a>', 'contact-form-7-multi-step-module' ) ), CF7MSM_LEARN_MORE_URL ); ?>
            </div>
        </div>
    </div><!-- #upgradediv -->
<?php
}


/**
 * Display review notice
 */
function cf7msm_review_notice() { 
    $notice_big_cookie = cf7msm_maybe_display_notice_big_cookie();
    if ( $notice_big_cookie ) {
        // big cookie notice is more important.  only display one at a time.
        return;
    }
    $notice_num = cf7msm_maybe_display_notice();
    if ( empty( $notice_num ) ) {
        return;
    }

    // don't show review if they need the pro version
    $stats = get_option( '_cf7msm_stats', array() );
    if ( !empty( $stats['big_cookies'] ) && cf7msm_fs()->is_free_plan() ) {
        return;
    }
?>
    <div class="notice notice-info cf7msm-notice cf7msm-notice-review">
        <div class="cf7msm-notice-inner">
            <div class="cf7msm-notice-icon">
                <img src="<?php echo cf7msm_url( '/resources/plugin-icon.png' ) ?>" width="64">
            </div>
            <div class="cf7msm-notice-content">
                <?php if ( $notice_num == 1 ) : ?>
                <h3><?php _e( 'Your Multi-Step Forms are doing great!', 'contact-form-7-multi-step-module' ); ?></h3>
                <p><?php _e( 'When you get a chance, could you help me out by leaving a 5-star review for the <strong>Contact Form 7 Multi-Step Forms</strong> plugin on WordPress?  It makes me feel awesome to know my plugin is helping others.', 'contact-form-7-multi-step-module' ); ?></p>
                <?php else : ?>
                <h3><?php _e( 'Wow!  Your Multi-Step Forms are taking off!', 'contact-form-7-multi-step-module' ); ?></h3>
                <p><?php _e( 'I would really appreciate if you could leave a 5-star review for the <strong>Contact Form 7 Multi-Step Forms</strong> plugin on WordPress.  It really helps to hear people are actually benefiting from my work.', 'contact-form-7-multi-step-module' ); ?></p>
                <?php endif; ?>
            </div>
            <div class="cf7msm-notice-actions">
                <a href="https://wordpress.org/support/view/plugin-reviews/contact-form-7-multi-step-module#new-post" class="button button-primary cf7msm-review-button" target="_blank">Leave a Review</a>
                <div class="other-buttons">
                    <a href="#" class="cf7msm-did">I already did</a>
                    <span class="spacer">|</span>
                    <a href="#" class="cf7msm-later">Maybe later</a>

                </div>
            </div>
        </div>
    </div>
<?php }
add_action( 'admin_notices', 'cf7msm_review_notice' );

/**
 * Display review notice
 */
function cf7msm_big_cookie_notice() { 
    $notice_big_cookie = cf7msm_maybe_display_notice_big_cookie();
    if ( !$notice_big_cookie ) {
        return;
    }
    $stats = get_option( '_cf7msm_stats', array() );
    if ( empty( $stats['big_cookies'] ) ) {
        return false;
    }
    $num_cookies_str = '' . $stats['big_cookies'];
    if ( $stats['big_cookies'] < 10 ) {
        $num_array = array( 
            '', 
            __( 'one', 'contact-form-7-multi-step-module' ), 
            __( 'two', 'contact-form-7-multi-step-module' ), 
            __( 'three', 'contact-form-7-multi-step-module' ), 
            __( 'four', 'contact-form-7-multi-step-module' ), 
            __( 'five', 'contact-form-7-multi-step-module' ), 
            __( 'six', 'contact-form-7-multi-step-module' ), 
            __( 'seven', 'contact-form-7-multi-step-module' ), 
            __( 'eight', 'contact-form-7-multi-step-module' ), 
            __( 'nine', 'contact-form-7-multi-step-module' ), 
            __( 'ten', 'contact-form-7-multi-step-module' )
        );
        $num_cookies_str = ucfirst( $num_array[$stats['big_cookies']] );
    }
?>
    <div class="notice notice-error cf7msm-notice cf7msm-notice-cookie">
        <div class="cf7msm-notice-inner">
            <div class="cf7msm-notice-icon">
                <img src="<?php echo cf7msm_url( '/resources/plugin-icon.png' ) ?>" width="64">
            </div>
            <div class="cf7msm-notice-content">
                <h3><?php _e( 'Your Multi-Step Forms are in danger of losing data!', 'contact-form-7-multi-step-module' ); ?></h3>
                <p><?php echo sprintf( __( '<strong>%s</strong> multi-step form submissions have exceeded 90%% of the standard browser\'s cookie size.  You may not be getting everything your users submit from your multi-step forms!' ), $num_cookies_str ); ?>
                    <span style="display: block; padding-top: 10px"><?php _e( 'Upgrade to the PRO version of the <strong>Contact Form 7 Multi-Step Forms</strong> plugin before this happens again!', 'contact-form-7-multi-step-module' ); ?></p>
            </div>
            <div class="cf7msm-notice-actions">
                <a href="<?php echo CF7MSM_LEARN_MORE_URL; ?>" class="button button-primary cf7msm-review-button" target="_blank"><?php _e( 'Upgrade to Pro', 'contact-form-7-multi-step-module' ); ?> <span class="external dashicons dashicons-external" ></span></a>


                <div class="other-buttons">
                    <a href="#" class="cf7msm-later"><?php _e( 'Remind me later if this continues', 'contact-form-7-multi-step-module' ); ?></a>

                    <a href="#" class="trash cf7msm-did"><?php _e( 'Don\'t show again', 'contact-form-7-multi-step-module '); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php }
add_action( 'admin_notices', 'cf7msm_big_cookie_notice' );

/**
 * Return a number for which notice to display depending on the timing and usage.
 */
function cf7msm_maybe_display_notice() {
    $stats = get_option( '_cf7msm_stats', array() );
    $notice = !empty( $stats['notice'] ) ? $stats['notice'] : '';
    if ( $notice == 'no' || $notice == 'yes' ) {
        return 0;
    }
    $install_date = !empty( $stats['install_date'] ) ? $stats['install_date'] : 0;
    $one_week_ago = ( time() - 604800 );
    if ( $install_date > $one_week_ago ) {
        return;
    }
    if ( !empty( $notice ) && $notice > $one_week_ago ) {
        return;
    }

    $count = !empty( $stats['count'] ) ? $stats['count'] : 0;

    if ( $count > 5 && !empty( $notice ) ) {
        return 2;
    }
    if ( $count > 3 ) {
        return 1;
    }

    
    return 0;
}

/**
 * Return a true if big cookie notice should be displayed
 */
function cf7msm_maybe_display_notice_big_cookie() {
    $stats = get_option( '_cf7msm_stats', array() );
    $notice = !empty( $stats['notice-big-cookie'] ) ? $stats['notice-big-cookie'] : '';
    if ( $notice == 'no' ) {
        return false;
    }
    if ( empty( $stats['big_cookies'] ) ) {
        return false;
    }

    $display = false;

    if ( empty( $notice ) ) {
        if ( $stats['big_cookies'] >= 2 ) {
            $display = true;
        }
    }
    else if ( $stats['big_cookies'] >= $notice + 2 ) {
        $display = true;
    }
    
    return $display;
}

/**
 * note at top of form tags
 */
function cf7msm_form_tag_header_text( $header_description ) {
    $description = $header_description . __( ". For more details, see %s.", 'contact-form-7' );
    $desc_link = wpcf7_link( 
        'https://wordpress.org/plugins/contact-form-7-multi-step-module/', 
        esc_html( __( 'the plugin page on WordPress.org', 'contact-form-7-multi-step-module' ) ), 
        array( 'target' => '_blank' )
    );
    printf( esc_html( $description ), $desc_link );
}

/**
 * Links to help the plugin.
 */
function cf7msm_form_tag_footer_text() {
    $url_donate = 'https://webheadcoder.com/donate-cf7-multi-step-forms';
    $url_review = 'https://wordpress.org/support/view/plugin-reviews/contact-form-7-multi-step-module#postform';
?>
    <p class="description" style="font-size:12px;margin-top:0;padding-top:0;font-style:normal;">
        <?php 
        printf( cf7msm_kses( 
            __( 'Like the Multi-step addition to CF7?  Let me know - <a href="%s" target="_blank">Donate</a> and <a href="%s" target="_blank">Review</a>.', 'contact-form-7-multi-step-module' )
            ), $url_donate, $url_review );
         ?>
    </p>
    <div style="position:absolute; right:25px; bottom:5px;">
        <a href="https://webheadcoder.com" target="_blank"><img src="<?php echo cf7msm_url( '/resources/logo.png' )?>" width="40"></a>
    </div>
<?php
}



/**
 * Log what the user said to the review notice.
 */
function cf7msm_notice_response() {
    if ( !check_ajax_referer('cf7msm-admin-nonce', 'nonce', false) ){
        wp_send_json( 0 );
    }
    if ( !isset( $_POST['request_type'] ) ) {
        wp_send_json( 0 );
    }
    $data = !empty( $_POST['request_type'] );
    $stats = get_option( '_cf7msm_stats', array() );
    if ( !$data ) {    
        $notice = !empty( $stats['notice'] ) ? $stats['notice'] : '';
        if ( empty( $notice ) ) {
            $stats['notice'] = time();
        }
        else {
            $stats['notice'] = 'no';
        }
    }
    elseif ( $data ) {
        $stats['notice'] = 'yes';
    }
    update_option( '_cf7msm_stats', $stats );
    wp_send_json( 1 );
}
add_action('wp_ajax_cf7msm-notice-response', 'cf7msm_notice_response');


/**
 * Log what the user said to the big cookie notice.
 */
function cf7msm_notice_response_big_cookie() {
    if ( !check_ajax_referer('cf7msm-admin-nonce', 'nonce', false) ){
        wp_send_json( 0 );
    }
    if ( !isset( $_POST['request_type'] ) ) {
        wp_send_json( 0 );
    }
    $data = !empty( $_POST['request_type'] );
    $stats = get_option( '_cf7msm_stats', array() );
    if ( empty( $stats['big_cookies'] ) ) {
        // shouldn't get here
        wp_send_json( 1 );
    }
    if ( !$data ) {    
        // set the notice marker to the last seen number of big cookies.
        $stats['notice-big-cookie'] = $stats['big_cookies'];
    }
    elseif ( $data ) {
        $stats['notice-big-cookie'] = 'no';
    }
    update_option( '_cf7msm_stats', $stats );
    wp_send_json( 1 );
}
add_action('wp_ajax_cf7msm-notice-response-big-cookie', 'cf7msm_notice_response_big_cookie');