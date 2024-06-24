$( 'html' ).on( 'click', '.change-iframe', function(){
                $( '#dynamic-iframe' )[0].contentWindow.location.replace( $( this ).data( 'src' ) );
            });