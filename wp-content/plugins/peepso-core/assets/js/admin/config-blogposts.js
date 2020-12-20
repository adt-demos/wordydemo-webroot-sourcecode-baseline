jQuery(function( $ ) {
	var $cbProfile = $( 'input[name=blogposts_profile_enable]' ),
		$cbAuthorbox = $( 'input[name=blogposts_authorbox_enable]' ),
		$cbActivity = $( 'input[name=blogposts_activity_enable]' ),
		$cbComment = $( 'input[name=blogposts_comments_enable]' );

	// Handle enable profile checkbox.
	$cbProfile.on( 'click', function() {
		var $field = $( this ).closest( '.form-group' ),
			$fields = $field.nextAll( '.form-group' ),
			$siblingBoxes = $field.closest( '.postbox' ).siblings( '.postbox' );

		// Also add the `.clearfix` element next to the field.
		$fields = $fields.map(function() {
			var $field = $( this );
			return [ this, $( this ).next( '.clearfix' ).get( 0 ) ];
		});

		if ( this.checked ) {
			$fields.show();
			$siblingBoxes.show();
			$cbAuthorbox.triggerHandler( 'click' );
		} else {
			$fields.hide();
			$siblingBoxes.hide();
		}
	});

	// Handle enable authorbox checkbox.
	$cbAuthorbox.on( 'click', function() {
		var $fields = $( 'input[name=blogposts_authorbox_author_name_pre_text]' ).closest( '.form-group' );

		// Also add the `.clearfix` element next to the field.
		$fields = $fields.add( $fields.next( '.clearfix' ) );

		if ( this.checked ) {
			$fields.show();
		} else {
			$fields.hide();
		}
	});

	// Handle enable activity checkbox.
	$cbActivity.on( 'click', function() {
		var $field = $( this ).closest( '.form-group' ),
			$fields = $field.nextAll( '.form-group' );

		// Also add the `.clearfix` element next to the field.
		$fields = $fields.map(function() {
			var $field = $( this );
			return [ this, $( this ).next( '.clearfix' ).get( 0 ) ];
		});

		if ( this.checked ) {
			$fields.show();
			$cbComment.triggerHandler( 'click' );
		} else {
			$fields.hide();
		}
	});

	// Handle enable comment checkbox.
	$cbComment.on( 'click', function() {
		var $field = $( this ).closest( '.form-group' ),
			$fields = $field.nextAll( '.form-group' );

		// Also add the `.clearfix` element next to the field.
		$fields = $fields.map(function() {
			var $field = $( this );
			return [ this, $( this ).next( '.clearfix' ).get( 0 ) ];
		});

		if ( this.checked ) {
			$fields.show()
		} else {
			$fields.hide();
		}
	});

	// Set initial visibility.
	$cbProfile.triggerHandler( 'click' );
	$cbActivity.triggerHandler( 'click' );

});
