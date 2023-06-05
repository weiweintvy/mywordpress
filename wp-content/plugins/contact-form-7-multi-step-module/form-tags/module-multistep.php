<?php
/*  Copyright 2016 Webhead LLC (email: info at webheadcoder.com)

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

/**
 * Initialize this wpcf7 shortcode.
 */
function cf7msm_add_shortcode_multistep() {
    if (function_exists('wpcf7_add_form_tag')) {
        wpcf7_add_form_tag(
            array( 'multistep' ),
            'cf7msm_multistep_shortcode_handler',
            true
        );
    }
    else if (function_exists('wpcf7_add_shortcode')) {
        wpcf7_add_shortcode(
            array( 'multistep' ),
            'cf7msm_multistep_shortcode_handler',
            true
        );
    }
}
add_action( 'wpcf7_init', 'cf7msm_add_shortcode_multistep' );

/**
 * Add to the wpcf7 tag generator.
 */
function cf7msm_add_tag_generator_multistep() {
    if ( class_exists( 'WPCF7_TagGenerator' ) ) {
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'multistep', esc_html( __( 'multistep', 'contact-form-7-multi-step-module' ) ), 'cf7msm_multistep_tag_generator' );
    }
}
add_action( 'admin_init', 'cf7msm_add_tag_generator_multistep', 30 );

/**
 * Handle the multistep handler
 * This shortcode lets the plugin determine if the form is a multi-step form
 * and if it should redirect the user to step 1.
 */
function cf7msm_multistep_shortcode_handler( $tag ) {
    $tag = new WPCF7_FormTag( $tag );
    if ( empty( $tag->name ) ) {
        return cf7msm_multistep_shortcode_handler_old( $tag );
    }

    $class = wpcf7_form_controls_class( $tag->type, 'cf7msm-multistep' );
    $class .= ' cf7msm-multistep';

    // TODO:  index 1 of values will be steps ("1-3") to prevent skipping steps
    $next_url = (string) reset( $tag->values );
    $options = array();
    if ( is_array( $tag->options ) ) {
        foreach ( $tag->options as $option ) {
            $options[$option] = 1;
        }
    }
    if ( !empty( $next_url ) ) {
        $options['next_url'] = $next_url;   
    }

    $atts = array(
        'type'               => 'hidden',
        'name'               => '_cf7msm_multistep_tag',
        'class'              => $tag->get_class_option( $class ),
        'value'              => '' . json_encode( $options )
    );
    $atts = wpcf7_format_atts( $atts );
    $html = sprintf( '<input %1$s />', $atts );
    $html .= sprintf( '<input %1$s />', wpcf7_format_atts( array( 
        'type'  => 'hidden', 
        'name'  => 'cf7msm-no-ss',
        'value' => ''
    ) ) );

    return $html;
}

function cf7msm_multistep_shortcode_handler_old( $tag ) {
    $class = wpcf7_form_controls_class( $tag->type, 'cf7msm-multistep' );
    $class .= ' cf7msm-multistep';
    if ( 'multistep*' === $tag->type ) {
        $class .= ' wpcf7-validates-as-required';
    }
    
    $value = (string) reset( $tag->values );

    $multistep_values = cf7msm_format_multistep_value( $value );
    $step_value = $multistep_values['curr_step'] . '-' . $multistep_values['total_steps'];

    $atts = array(
        'type'               => 'hidden',
        'class'              => $tag->get_class_option( $class ),
        'value'              => $step_value,
        'name'               => 'cf7msm-step'
    );
    $atts = wpcf7_format_atts( $atts );
    $html = sprintf( '<input %1$s />', $atts );
    $html .= sprintf( '<input %1$s />', wpcf7_format_atts( array( 
        'type'  => 'hidden', 
        'name'  => 'cf7msm-no-ss',
        'value' => ''
    ) ) );

    return $html;
}

/**
 * Multistep tag pane.
 */
function cf7msm_multistep_tag_generator( $contact_form, $args = '' ) {

    $args = wp_parse_args( $args, array() );
?>
<div class="control-box cf7msm-multistep">
    <fieldset>
        <legend><?php cf7msm_form_tag_header_text( 'Generate a form-tag to enable a multistep form' ); ?></legend>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                    <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
                            </td>
                </tr>
                <tr>
                    <th scope="row"><label for="first_step"><?php echo esc_html( __( 'First Step', 'contact-form-7-multi-step-module' ) ); ?></label>
                    </th>
                    <td><input type="checkbox" name="first_step" class="option" id="first_step" /> &nbsp;
                        <label for="first_step"><span class="description"><?php echo esc_html( __( 'Check this if this form is the first step.' ) ) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="last_step"><?php echo esc_html( __( 'Last Step', 'contact-form-7-multi-step-module' ) ); ?></label>
                    </th>
                    <td><input type="checkbox" name="last_step" class="option" id="last_step" /> &nbsp;
                        <label for="last_step"><span class="description"><?php echo esc_html( __( 'Check this if this form is the last step.' ) ) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="send_email"><?php echo esc_html( __( 'Send Email', 'contact-form-7-multi-step-module' ) ); ?></label>
                    </th>
                    <td><input type="checkbox" name="send_email" class="option" id="send_email" /> &nbsp;
                        <label for="send_email"><span class="description"><?php echo esc_html( __( 'Send email after this form submits.' ) ) ?></span></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="skip_save"><?php echo esc_html( __( 'Skip Save', 'contact-form-7-multi-step-module' ) ); ?></label>
                    </th>
                    <td><input type="checkbox" name="skip_save" class="option" id="skip_save" /> &nbsp;
                        <label for="skip_save"><span class="description"><?php echo esc_html( __( 'Don\'t save this form to the database (for Flamingo and CFDB7).' ) ) ?></span></label>
                    </td>
                </tr>

                <tr><td><br></td></tr>
                <tr>
                    <th scope="row">
                        <?php _e('Next Page URL', 'cf7msm'); ?>
                    </th>
                    <td>
                        <input id="tag-generator-panel-next-url" type="text" name="values" class="oneline cf7msm-url" />
                        <br>
                        <label for="tag-generator-panel-next-url">
                            <span class="description"><?php echo esc_html( __( 'The URL of the page that contains the next form.', 'contact-form-7-multi-step-module' ) ) ?><br>
                                <?php echo esc_html( __( 'This can be blank on the last step.' ) ); ?></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="cf7msm-faq" style="display:none;">
            <?php if ( cf7msm_fs()->is_not_paying() ) : ?>
            <?php printf( cf7msm_kses( __( '<p><strong>Warning:</strong> Your form may be at risk of being too large for the free version of this plugin.<br>If a user submits too much data in the forms you may not get all information.<br><button class="cf7msm-freemius-purchase">Upgrade Now</button><br><a href="%s" target="_blank">See here for more information.</a></p>', 'contact-form-7-multi-step-module' ) ), CF7MSM_LEARN_MORE_URL ); ?>
            <?php endif; ?>
        </div>
    </fieldset>
</div>
    <div class="insert-box">
        <input type="hidden" name="values" value="" />
        <input type="text" name="multistep" class="tag code" readonly="readonly" onfocus="this.select()" />

        <div class="submitbox">
            <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7-multi-step-module' ) ); ?>" />
        </div>

        <br class="clear" />

        <p class="description mail-tag"><label><?php echo esc_html( __( "This field should not be used on the Mail tab.", 'contact-form-7-multi-step-module' ) ); ?></label>
        </p>
        <?php cf7msm_form_tag_footer_text();?>
    </div>
<?php
}


/**
 * Error messages if first step is not set and user did not already visit the first step.
 */
function cf7msm_multistep_messages( $messages ) {
    $messages = array_merge( $messages, array(
        'invalid_first_step' => array(
            'description' =>
                __( "The sender visited this form without submitting the first step of the multistep forms.", 'contact-form-7-multi-step-module' ),
            'default' =>
                __( "Please fill out the form on the previous page.", 'contact-form-7-multi-step-module' ),
        ),
    ) );

    return $messages;
}
add_filter( 'wpcf7_messages', 'cf7msm_multistep_messages' );

/**
 * Return the step value and next url in an array.  URL may be empty.
 */
function cf7msm_format_multistep_value( $valueString ) {
    $no_url = false;
    $next_url = '';

    $i = stripos( $valueString, '-' );
    $curr_step = substr( $valueString, 0, $i );
    $j = stripos( $valueString, '-', $i+1 );
    if ( $j === FALSE ) {
        $j = strlen( $valueString );
        $no_url = true;
    }
    $total_steps = substr( $valueString, $i+1, $j-($i+1) );
    if ( !$no_url ) {
        $next_url = substr( $valueString, $j+1 );
    }

    return array(
        'curr_step'   => $curr_step,
        'total_steps' => $total_steps,
        'next_url'    => $next_url
    );
}