/* global wp */
( function( $, _ ) {

	// Bail when method already exists
	if ( typeof wp.media.wpSettingImage !== 'undefined' )
		return;

	/**
	 * The Setting Image constructor which creates instances for the given image
	 *
	 * @since 1.0.0
	 *
	 * @param {object} image Image details
	 */
	wp.media.wpSettingImage = function( image ) {
		var Attachment = wp.media.model.Attachment,
		    FeaturedImage = wp.media.controller.FeaturedImage;

		// Define collection of settingImages controller and media instances
		if ( typeof wp.media.controller.settingImages === 'undefined' ) {
			wp.media.controller.settingImages = {};
			wp.media.settingImages = {};
		}

		/**
		 * Construct implementation of the FeaturedImage modal controller
		 * for the Setting Image
		 */
		wp.media.controller.settingImages[ image.name ] = FeaturedImage.extend({
			defaults: _.defaults({
				id:      image.key,
				title:   image.l10n.settingImageTitle,
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
					id = wp.media.view.settings.settingImages[ image.settingKey ].image,
					attachment;

				if ( '' !== id && -1 !== id ) {
					attachment = Attachment.get( id );
					attachment.fetch();
				}

				selection.reset( attachment ? [ attachment ] : [] );
			}
		});

		/**
		 * wp.media.settingImages
		 * @namespace
		 *
		 * @see wp.media.featuredImage wp-includes/js/media-editor.js
		 */
		wp.media.settingImages[ image.name ] = {
			/**
			 * Set the setting image id, save the setting image data and
			 * set the HTML in the setting meta box to the new setting image.
			 *
			 * @global wp.media.view.settings
			 * @global wp.media.post
			 *
			 * @param {number} id The post ID of the setting image, or -1 to unset it.
			 */
			set: function( id ) {
				var settings = wp.media.view.settings.settingImages, key = image.settingKey;

				settings[ key ].image = id;

				wp.media.post( image.ajaxAction, {
					json:             true,
					setting_key:      key,
					setting_image_id: settings[ key ].image,
					_wpnonce:         settings[ key ].nonce,
				}).done( function( resp ) {
					if ( resp == '0' ) {
						window.alert( image.l10n.error );
						return;
					}
					$( '.wp-setting-image', image.parentEl )
						.toggleClass( 'has-image', resp.setImageClass )
						.html( resp.html );
				});
			},
			/**
			 * Remove the setting image id, save the setting image data and
			 * set the HTML in the setting meta box to no setting image.
			 */
			remove: function() {
				wp.media.settingImages[ image.name ].set( -1 );
			},
			/**
			 * The Setting Image workflow
			 *
			 * @global wp.media.controller.FeaturedImage
			 * @global wp.media.view.l10n
			 *
			 * @this wp.media.settingImages
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
					states: [ new wp.media.controller.settingImages[ image.name ](), new wp.media.controller.EditImage() ]
				});

				this._frame.on( 'toolbar:create:' + image.key, function( toolbar ) {
					/**
					 * @this wp.media.view.MediaFrame.Select
					 */
					this.createSelectToolbar( toolbar, {
						text: image.l10n.setSettingImage
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
			 * 'select' callback for Setting Image workflow, triggered when
			 *  the 'Set Setting Image' button is clicked in the media modal.
			 *
			 * @global wp.media.view.settings
			 *
			 * @this wp.media.controller.FeaturedImage
			 */
			select: function() {
				var selection = this.get('selection').single();

				if ( ! wp.media.view.settings.settingImages[ image.settingKey ] ) {
					return;
				}

				wp.media.settingImages[ image.name ].set( selection ? selection.id : -1 );
			},
			/**
			 * Open the content media manager to the 'setting image' tab when
			 * the setting image is clicked.
			 *
			 * Update the setting image id when the 'remove' link is clicked.
			 *
			 * @global wp.media.view.settings
			 */
			init: function() {
				$( image.parentEl ).on( 'click', '.wp-setting-image-set', function( event ) {
					event.preventDefault();
					// Stop propagation to prevent thickbox from activating.
					event.stopPropagation();

					wp.media.settingImages[ image.name ].frame().open();
				}).on( 'click', '.wp-setting-image-remove', function() {
					wp.media.settingImages[ image.name ].remove();
					return false;
				});
			},
		};

		$( wp.media.settingImages[ image.name ].init );
	};

}( jQuery, _ ) );
