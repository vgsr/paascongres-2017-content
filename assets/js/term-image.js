/* global wp */
( function( $, _ ) {

	// Bail when method already exists
	if ( typeof wp.media.wpTermImage !== 'undefined' )
		return;

	/**
	 * The Term Image constructor which creates instances for the given image
	 *
	 * @since 1.0.0
	 *
	 * @param {object} image Image details
	 */
	wp.media.wpTermImage = function( image ) {
		var Attachment = wp.media.model.Attachment,
		    FeaturedImage = wp.media.controller.FeaturedImage;

		// Define collection of termImages controller and media instances
		if ( typeof wp.media.controller.termImages === 'undefined' ) {
			wp.media.controller.termImages = {};
			wp.media.termImages = {};
		}

		/**
		 * Construct implementation of the FeaturedImage modal controller
		 * for the Term Image
		 */
		wp.media.controller.termImages[ image.name ] = FeaturedImage.extend({
			defaults: _.defaults({
				id:      image.key,
				title:   image.l10n.termImageTitle,
				toolbar: image.key
			}, FeaturedImage.prototype.defaults ),

			/**
			 * Overload the controller's native initializer method to modify
			 * the collection's mime type.
			 */
			initialize: function() {

				// If we haven't been provided a `library`, create a `Selection`.
				if ( ! this.get('library') ) {
					this.set( 'library', wp.media.query({ type: image.mimeType }) );
				}

				FeaturedImage.prototype.initialize.apply( this, arguments );
			},

			/**
			 * Overload the controller's native selection updater method
			 *
			 * @this wp.media.controller.FeaturedImage (Library)
			 */
			updateSelection: function() {
				var selection = this.get('selection'),
				    term = wp.media.termImages[ image.name ].term(),
					id = wp.media.view.settings.termImages[ image.metaKey ][ term ].image,
					attachment;

				if ( '' !== id && -1 !== id ) {
					attachment = Attachment.get( id );
					attachment.fetch();
				}

				selection.reset( attachment ? [ attachment ] : [] );
			}
		});

		/**
		 * wp.media.termImages
		 * @namespace
		 *
		 * @see wp.media.featuredImage wp-includes/js/media-editor.js
		 */
		wp.media.termImages[ image.name ] = {
			/**
			 * Set the term image id, save the term image data and
			 * set the HTML in the post meta box to the new term image.
			 *
			 * @global wp.media.view.settings
			 * @global wp.media.post
			 *
			 * @param {number} id The post ID of the term image, or -1 to unset it.
			 */
			set: function( id ) {
				var settings = wp.media.view.settings.termImages[ image.metaKey ], term = this.term();

				settings[ term ].image = id;

				wp.media.post( image.ajaxAction, {
					json:          true,
					term_id:       term,
					term_image_id: settings[ term ].image,
					_wpnonce:      settings[ term ].nonce,
				}).done( function( resp ) {
					if ( resp == '0' ) {
						window.alert( image.l10n.error );
						return;
					}
					$( '.wp-term-image', image.parentEl ).filter( image.wrapEl + ' [data-term="' + term + '"]' )
						.toggleClass( 'has-image', resp.setImageClass )
						.html( resp.html );
					settings[ term ].nonce = resp.nonce;
				});
			},
			/**
			 * Remove the post image id, save the post image data and
			 * set the HTML in the post meta box to no post image.
			 */
			remove: function( term ) {
				wp.media.termImages[ image.name ].set( -1 );
			},
			/**
			 * The Term Image workflow
			 *
			 * @global wp.media.controller.FeaturedImage
			 * @global wp.media.view.l10n
			 *
			 * @this wp.media.termImages
			 *
			 * @returns {wp.media.view.MediaFrame.Select} A media workflow.
			 */
			frame: function() {
				if ( this._frame ) {
					wp.media.frame = this._frame;
					return this._frame;
				}

				this._frame = wp.media({
					state:  image.key,
					states: [ new wp.media.controller.termImages[ image.name ](), new wp.media.controller.EditImage() ]
				});

				this._frame.on( 'toolbar:create:' + image.key, function( toolbar ) {
					/**
					 * @this wp.media.view.MediaFrame.Select
					 */
					this.createSelectToolbar( toolbar, {
						text: image.l10n.setTermImage
					});
				}, this._frame );

				this._frame.on( 'content:render:edit-image', function() {
					var selection = this.state( image.key ).get('selection'),
						view = new wp.media.view.EditImage( { model: selection.single(), controller: this } ).render();

					this.content.set( view );

					// after bringing in the frame, load the actual editor via an ajax call
					view.loadEditor();

				}, this._frame );

				this._frame.state( image.key ).on( 'select', this.select );
				return this._frame;
			},
			/**
			 * 'select' callback for Term Image workflow, triggered when
			 *  the 'Set Term Image' button is clicked in the media modal.
			 *
			 * @global wp.media.view.settings
			 *
			 * @this wp.media.controller.FeaturedImage
			 */
			select: function() {
				var selection = this.get('selection').single();

				if ( ! wp.media.view.settings.termImages[ image.metaKey ] ) {
					return;
				}

				wp.media.termImages[ image.name ].set( selection ? selection.id : -1 );
			},
			/**
			 * Open the content media manager to the 'term image' tab when
			 * the term image is clicked.
			 *
			 * Update the term image id when the 'remove' link is clicked.
			 *
			 * @global wp.media.view.settings
			 */
			init: function() {
				$( image.parentEl ).on( 'click', image.wrapEl + ' .wp-term-image-set', function( event ) {
					event.preventDefault();
					// Stop propagation to prevent thickbox from activating.
					event.stopPropagation();

					wp.media.termImages[ image.name ].context( event ).frame().open();
				}).on( 'click', image.wrapEl + ' .wp-term-image-remove', function( event ) {
					wp.media.termImages[ image.name ].context( event ).remove();
					return false;
				});
			},
			/**
			 * Return the id from the contexted term
			 *
			 * @returns {number} Term id
			 */
			term: function() {
				return this._term;
			},
			/**
			 * Set the term id from the provided event context
			 *
			 * @param {object} event The context's event object
			 * @returns {wp.media.termImages} Returns itself to allow chaining
			 */
			context: function( event ) {
				var $term  = $( event.target ).parents( '.wp-term-image' );
				this._term = $term.data( 'term' );

				// Add newly created terms to the global settings
				if ( typeof wp.media.view.settings.termImages[ image.metaKey ][ this._term ] === 'undefined' ) {
					wp.media.view.settings.termImages[ image.metaKey ][ this._term ] = {
						'image': -1,
						'nonce': $term.data( 'nonce' )
					};
				}

				return this;
			}
		};

		$( wp.media.termImages[ image.name ].init );
	};

}( jQuery, _ ) );
