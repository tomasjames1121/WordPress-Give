<?php
namespace Give\Views\Form\Themes\Legacy;

use Give\Form\Theme;

class Legacy extends Theme {

	/**
	 * @inheritDoc
	 */
	public function getID() {
		return 'legacy';
	}

	/**
	 * @inheritDoc
	 */
	public function geName() {
		return __( 'Legacy - Standard Form', 'give' );
	}

	/**
	 * @inheritDoc
	 */
	public function getImage() {
		return 'https://images.unsplash.com/photo-1510070112810-d4e9a46d9e91?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=300&q=80';
	}

	/**
	 * @inheritDoc
	 */
	public function getOptions() {
		return require 'optionConfig.php';
	}
}
