<?php

class WP_Export_Oxymel extends Oxymel {
	public function optional( $tag_name, $contents ) {
		if ( $contents ) {
			$this->$tag_name( $contents );
		}
		return $this;
	}

	public function optional_cdata( $tag_name, $contents ) {
		if ( $contents ) {
			$this->$tag_name->contains->cdata( $contents )->end;
		}
		return $this;
	}

	public function cdata( $text ) {
		if ( is_string( $text ) && ! seems_utf8( $text ) ) {
			$text = mb_convert_encoding( $text, 'UTF-8' );
		}
		return parent::cdata( $text );
	}
}
