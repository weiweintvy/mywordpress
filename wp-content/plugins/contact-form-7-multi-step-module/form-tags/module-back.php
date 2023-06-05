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
function cf7msm_add_shortcode_back() {
    if (function_exists('wpcf7_add_form_tag')) {
        wpcf7_add_form_tag(
            array( 'back', 'previous' ),
            'cf7msm_back_shortcode_handler'
        );
    }
    else if (function_exists('wpcf7_add_shortcode')) {
        wpcf7_add_shortcode(
            array( 'back', 'previous' ),
            'cf7msm_back_shortcode_handler'
        );
    }
}
add_action( 'wpcf7_init', 'cf7msm_add_shortcode_back' );

/**
 * Handle the back form shortcode.
 */
function cf7msm_back_shortcode_handler( $tag ) {
	if (!class_exists('WPCF7_FormTag') || !function_exists('wpcf7_form_controls_class'))
		return;
	$tag = new WPCF7_FormTag( $tag );

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
    $atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';
	if ( empty( $value ) ) {
		if ( $tag->type == 'previous') {
			$value = esc_html( __( 'Previous', 'contact-form-7-multi-step-module' ) );
		}
		else {
			//using old version
			$value = esc_html( __( 'Back', 'contact-form-7-multi-step-module' ) );
		}
	}

	$atts['type'] = 'button';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}


/**
 * Add to the wpcf7 tag generator.
 */
function cf7msm_add_tag_generator_back() {
    if ( class_exists( 'WPCF7_TagGenerator' ) ) {
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'previous', esc_html( __( 'previous', 'contact-form-7-multi-step-module' ) ),
            'cf7msm_previous_tag_pane', array( 'nameless' => 1 ) );
    }
    else if ( function_exists( 'wpcf7_add_tag_generator' ) ) {
		wpcf7_add_tag_generator( 'back', esc_html( __( 'Back button', 'contact-form-7-multi-step-module' ) ),
			'wpcf7-cf7msm-back', 'wpcf7_cf7msm_back', array( 'nameless' => 1 ) );
    }
}
add_action( 'admin_init', 'cf7msm_add_tag_generator_back', 55 );



/**
 * Multistep tag pane.
 */
function cf7msm_previous_tag_pane( $contact_form, $args = '' ) {

    $args = wp_parse_args( $args, array() );
?>
<div class="control-box cf7msm-multistep">
    <fieldset>
        <legend><?php cf7msm_form_tag_header_text( esc_html( __( 'Generate a form-tag for a previous button for a multistep form', 'contact-form-7-multi-step-module' ) ) ); ?></legend>
        <table class="form-table">
        <tbody>
            <tr>
            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'contact-form-7-multi-step-module' ) ); ?></label></th>
            <td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" />
                <label for="tag-generator-panel-previous">
                    <span class="description"><?php echo esc_html( __( 'The label on the button.', 'contact-form-7-multi-step-module' ) ); ?></span>
                </label></td>
            </tr>

            <tr>
            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7-multi-step-module' ) ); ?></label></th>
            <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
            </tr>

            <tr>
            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7-multi-step-module' ) ); ?></label></th>
            <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
            </tr>

        </tbody>
        </table>
    </fieldset>
</div>
    <div class="insert-box">
        <input type="text" name="previous" class="tag code" readonly="readonly" onfocus="this.select()" />

        <div class="submitbox">
            <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7-multi-step-module' ) ); ?>" />
        </div>

        <br class="clear" />

        <p class="description mail-tag"><label><?php echo esc_html( __( 'This field should not be used on the Mail tab.', 'contact-form-7-multi-step-module' ) ); ?></label></p>
        <?php cf7msm_form_tag_footer_text();?>
    </div>
<?php
}

/**
 * Deprecated way to generate back tag.
 */
function wpcf7_cf7msm_back( $contact_form ) {
?>
<div id="wpcf7-cf7msm-back" class="hidden">
<form action="">
<table>
<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7-multi-step-module' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>

<td><code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7-multi-step-module' ) ); ?>)<br />
<input type="text" name="class" class="classvalue oneline option" /></td>
</tr>

<tr>
<td><?php echo esc_html( __( 'Label', 'wpcf7' ) ); ?> (<?php echo esc_html( __( 'optional', 'contact-form-7-multi-step-module' ) ); ?>)<br />
<input type="text" name="values" class="oneline" /></td>

<td></td>
</tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( 'Copy this code and paste it into the form left.', 'contact-form-7-multi-step-module' ) ); ?><br /><input type="text" name="back" class="tag" readonly="readonly" onfocus="this.select()" /></div>
</form>
</div>
<?php
}

?>
