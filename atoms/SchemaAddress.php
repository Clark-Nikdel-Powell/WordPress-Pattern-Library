<?php
namespace CNP;

class SchemaAddress extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->tag = 'div';

		if ( ! isset( $data['address_data'] ) ) {
			return false;
		}

		$address_pieces = [ ];

		if ( isset( $data['address_data']['street_address'] ) ) {
			$address_pieces[] = '<span itemprop="streetAddress">' . $data['address_data']['street_address'] . '</span>';
		}

		if ( isset( $data['address_data']['city'] ) ) {
			$address_pieces[] = '<span itemprop="addressLocality">' . $data['address_data']['city'] . ',</span>';
		}

		if ( isset( $data['address_data']['state'] ) ) {
			$address_pieces[] = '<span itemprop="addressRegion">' . $data['address_data']['state'] . '</span>';
		}

		if ( isset( $data['address_data']['zip_code'] ) ) {
			$address_pieces[] = '<span itemprop="postalCode">' . $data['address_data']['zip_code'] . '</span>';
		}

		$address = '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">'. implode("", $address_pieces) .'</div>';

		$this->content = $address;

	}
}