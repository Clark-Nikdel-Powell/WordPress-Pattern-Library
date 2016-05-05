<?php
namespace CNP;

class Helpers {

	/**
	 * setBackgroundOnStructureArray.
	 *
	 * Set a background, no matter what kind of organism you're dealing with! Interchangeable between slides, headers, blurbs, whatever!
	 *
	 * TODO: standardize "background_type" field options across all layouts.
	 *
	 * @param $data
	 * @param $background_key
	 * @param $structure_array
	 *
	 * @return mixed
	 */
	public static function setBackgroundOnStructureArray( $data, $background_key, $structure_array ) {

		$background_type = $data['background_type'];

		// @EXIT: If there is no background, get rid of the atom.
		if ( 'None' === $background_type ) {
			unset( $structure_array[ $background_key ] );
			return $structure_array;
		}

		if ( 'Image' === $background_type ) {

			// @EXIT: If there's no image set, get rid of the atom.
			if ( empty( $data['image'] ) ) {
				unset( $structure_array[ $background_key ] );
				return $structure_array;
			}

			// Items are handled one-by-one so we don't accidentally overwrite preset array values.
			$structure_array[ $background_key ]['atom'] = 'Image';

			// Attachment ID
			$structure_array[ $background_key ]['attachment_id'] = $data['image'];

			// Image size: preset to full, TODO: should make sure we have a way to filter if necessary
			$structure_array[ $background_key ]['size'] = 'full';

			// Background images are often 100% of the viewport.
			$structure_array[ $background_key ]['attributes'] = [
				'sizes' => '100vw'
			];

		}

		if ( 'Video' === $background_type ) {

			// If there's no video or image files, get rid of the atom.
			if ( empty( $data['mp4'] ) && empty( $data['webm'] ) && empty( $data['jpg'] ) ) {
				unset( $structure_array[ $background_key ] );

				return $structure_array;
			}

			$slide_arg['structure'][ $background_key ]['atom'] = 'BackgroundVideo';

			if ( ! empty( $data['mp4'] ) ) {
				$structure_array[ $background_key ]['mp4'] = 'mp4:' . $data['mp4']['url'];
			}
			if ( ! empty( $data['webm'] ) ) {
				$structure_array[ $background_key ]['webm'] = 'webm:' . $data['webm']['url'];
			}
			if ( ! empty( $data['jpg'] ) ) {
				$structure_array[ $background_key ]['jpg'] = 'poster:' . $data['jpg']['url'];
			}
		}

		if ( 'Color' === $background_type ) {

			// If there's no color set, get rid of the atom.
			if ( empty( $data['background_color'] ) ) {
				unset( $structure_array[ $background_key ] );

				return $structure_array;
			}

			$structure_array[ $background_key ]['attributes']['style'] = 'background-color: ' . $data['background_color'] . ';';

		}

		return $structure_array;

	}
}