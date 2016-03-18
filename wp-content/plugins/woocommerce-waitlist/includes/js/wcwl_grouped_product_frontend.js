// JS required for grouped products on the front end
jQuery( document ).ready( function( $ ){

	// When email input is changed update the buttons href attribute to include the email
	$( "#wcwl_email" ).on( "input", function( e ) {
		var a_href = $( "a.woocommerce_waitlist" ).attr( "href" );
		var wcwl_email = $( "#wcwl_email" ).val();
			$( "a.woocommerce_waitlist" ).prop( "href", a_href+"&wcwl_email="+wcwl_email );
	});

	// Create arrays for the checkboxes
	var checked_array =  $( "input:checkbox:checked.wcwl_checkbox" ).map( function() {
  								return $( this ).attr( "id" );
							}).get();
	var unchecked_array = $( "input:checkbox:not(:checked).wcwl_checkbox" ).map( function() {
  								return $( this ).attr( "id" );
							 }).get();
	var changed = [];

	// When a checkbox is clicked, retrieve the product id for that checkbox and add/remove it from the 'changed' array
	// Modify the buttons href attribute to include the updated array of checkboxes in order to update the waitlist
	$( ".wcwl_checkbox" ).change( function() {
    	if( this.checked ) {
        	var checked = $( this ).attr( "id" );
        	if( $.inArray( checked, changed ) !== -1 ) {
        		changed.splice( $.inArray( checked, changed ), 1 );
        	}
        	else {
        		if( $.inArray( checked, checked_array ) == -1 ) {
        			changed.push( checked );
        		}
        	}
    	}
    	if( !this.checked ) {
        	var unchecked =  $( this ).attr( "id" );
        	if( $.inArray( unchecked, changed ) !== -1 ) {
        		changed.splice( $.inArray(unchecked, changed ), 1 );
        	}
        	else {
        		if( $.inArray( unchecked, unchecked_array ) == -1 ) {
        			changed.push( unchecked );
        		}
        	}
    	}
    	var a_href = $( "a.woocommerce_waitlist" ).attr( "href" );
        $( "a.woocommerce_waitlist" ).prop( "href", a_href+"&wcwl_changed="+changed );
	});
});
