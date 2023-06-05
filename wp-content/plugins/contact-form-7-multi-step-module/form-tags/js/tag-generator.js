(function($) {
    $('form.tag-generator-panel .cf7msm-multistep .cf7msm-multistep-values').change(function(){
        var current_step = $('input[name="values_current_step"]', $(this.form)).val();
        var total_steps = $('input[name="values_total_steps"]', $(this.form)).val();
        var next_url = $('input[name="next_url"]', $(this.form)).val();
        var value = current_step + '-' + total_steps;
        if ( next_url.length > 0 ) {
            value += '-' + next_url;
        }
        $('input[name="values"]', $(this.form)).val( value );

        if ( total_steps > 3 ) {
            $(".cf7msm-faq", $(this.form)).fadeIn();
        }
        else {
            $(".cf7msm-faq", $(this.form)).fadeOut();            
        }
    });

    $('form.tag-generator-panel .cf7msm-multistep input[name="last_step"]').change(function() {
        if ($(this).is(":checked")) {
            $('input[name="send_email"]', $(this.form)).prop('checked', true);
        }
    });
        
    
})(jQuery);