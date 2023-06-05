jQuery(function($) {
  $( document ).on( 'click', '.cf7msm-notice-review .cf7msm-later', function ( e ) {
        e.preventDefault();
        send_notice_request( 0 );
  } );
  $( document ).on( 'click', '.cf7msm-notice-review .cf7msm-did, .cf7msm-notice-review .cf7msm-review-button', function ( e ) {
          e.preventDefault();
          send_notice_request( 1 );
  } );

  function send_notice_request( request_type ) {
      // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      $.post( ajaxurl, {
            action: 'cf7msm-notice-response',
            request_type: request_type,
            nonce: cf7msm_admin.nonce,
          }, function( data ){
            if ( data != 0 ) {
                $('.cf7msm-notice-review').hide();
            }
          }, 'json' );
  }


  $( document ).on( 'click', '.cf7msm-notice-cookie .cf7msm-later', function ( e ) {
        e.preventDefault();
        send_cookie_notice_request( 0 );
  } );
  $( document ).on( 'click', '.cf7msm-notice-cookie .trash', function ( e ) {
          e.preventDefault();
          if ( window.confirm( 'Are you sure you don\'t want to be notified when your form submissions may be losing data?' ) ) {
            send_cookie_notice_request( 1 );
          }
  } );

  function send_cookie_notice_request( request_type ) {
      $.post( ajaxurl, {
            action: 'cf7msm-notice-response-big-cookie',
            request_type: request_type,
            nonce: cf7msm_admin.nonce,
          }, function( data ){
            if ( data != 0 ) {
                $('.cf7msm-notice-cookie').hide();
            }
          }, 'json' );
  }
});