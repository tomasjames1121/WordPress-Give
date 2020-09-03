<?php

namespace Give\Milestones;


class Model {

	// Settings
	protected $title;
	protected $description;
	protected $image;

	/**
	 * Constructs and sets up setting variables for a new Milestone model
	 *
	 * @param array $args Arguments for new Milestone, including 'ids' and 'title'
	 * @since 2.9.0
	 **/
	public function __construct( array $args ) {
		isset( $args['title'] ) ? $this->title             = $args['title'] : $this->title = __( 'Sample Milestone Title', 'give' );
		isset( $args['description'] ) ? $this->description = $args['description'] : $this->description = __( 'This is a sample description.', 'give' );
		isset( $args['image'] ) ? $this->image             = $args['image'] : $this->image = '';
	}

	/**
	 * Get output markup for Milestone
	 *
	 * @return string
	 * @since 2.9.0
	 **/
	public function getOutput() {
		ob_start();
		$output = '';
		require $this->getTemplatePath();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Get title for Milestone
	 *
	 * @return string
	 * @since 2.9.0
	 **/
	protected function getTitle() {
		return $this->title;
	}

	/**
	 * Get description for Milestone
	 *
	 * @return string
	 * @since 2.9.0
	 **/
	protected function getDescription() {
		return $this->description;
	}

	/**
	 * Get template path for Milestone component template
	 *
	 * @return string
	 * @since 2.9.0
	 **/
	protected function getImage() {
		return $this->image;
	}

	/**
	 * Get template path for Milestone component template
	 * @since 2.9.0
	 **/
	public function getTemplatePath() {
		return GIVE_PLUGIN_DIR . '/src/Milestones/resources/views/milestone.php';
	}
}
