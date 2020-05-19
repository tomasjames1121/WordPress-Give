<?php

namespace Give\Receipt;

use ArrayAccess;
use Iterator;

/**
 * Class Receipt
 *
 * This class represent receipt as object.
 * Receipt can have multiple detail group and detail group can has multiple detail item.
 *
 * @since 2.7.0
 * @package Give\Receipt
 */
abstract class Receipt implements Iterator, ArrayAccess {
	/**
	 * Receipt Heading.
	 *
	 * @since 2.7.0
	 * @var string $heading
	 */
	public $heading = '';

	/**
	 * Receipt message.
	 *
	 * @since 2.7.0
	 * @var string $message
	 */
	public $message = '';

	/**
	 * Receipt details group class names.
	 *
	 * @since 2.7.0
	 * @var array
	 */
	protected $sectionList = [];

	/**
	 * Get receipt sections.
	 *
	 * @return array
	 * @since 2.7.0
	 */
	public function getSections() {
		return $this->sectionList;
	}

	/**
	 * Add detail group.
	 *
	 * @param  array $section
	 *
	 * @since 2.7.0
	 */
	abstract public function addSection( $section );

	/**
	 * Remove receipt section.
	 *
	 * @param  string $sectionId
	 *
	 * @since 2.7.0
	 */
	abstract public function removeSection( $sectionId );
}
