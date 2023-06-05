<?php

/**
 * Remove br from hidden tags.
 */
function cf7msm_wpcf7_form_elements_return_false($form) {
    return preg_replace_callback('/<p>(<input\stype="hidden"\sname="_cf7msm_multistep_tag"(?:.*?))<\/p>/ism', 'cf7msm_wpcf7_form_elements_return_false_callback', $form);
}
add_filter('wpcf7_form_elements', 'cf7msm_wpcf7_form_elements_return_false');

function cf7msm_wpcf7_form_elements_return_false_callback($matches = array()) {
    return "\n".'<!-- CF7MSM -->'."\n".'<div style=\'display:none;\'>'.str_replace('<br>', '', str_replace('<br />', '', stripslashes_deep($matches[1]))).'</div>'."\n".'<!-- End CF7MSM -->'."\n";
}