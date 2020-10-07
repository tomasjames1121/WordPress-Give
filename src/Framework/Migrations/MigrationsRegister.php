<?php

namespace Give\Framework\Migrations;

use Give\Framework\Migrations\Contracts\Migration;
use http\Exception\InvalidArgumentException;

class MigrationsRegister {
	/**
	 * FQCN of Migration classes
	 *
	 * @since 2.9.0
	 *
	 * @var string[]
	 */
	private $migrations = [];

	/**
	 * Returns all of the registered migrations
	 *
	 * @since 2.9.0
	 *
	 * @return string[]
	 */
	public function getMigrations() {
		return $this->migrations;
	}

	/**
	 * Returns all of the registered migration ids
	 *
	 * @since 2.9.0
	 *
	 * @return string[]
	 */
	public function getRegisteredIds() {
		return array_keys( $this->migrations );
	}

	/**
	 * Add a migration to the list of migrations
	 *
	 * @since 2.9.0
	 *
	 * @param string $migrationClass FQCN of the Migration Class
	 */
	public function addMigration( $migrationClass ) {
		if ( ! is_subclass_of( $migrationClass, Migration::class ) ) {
			throw new InvalidArgumentException( 'Class must extend the ' . Migration::class . ' class' );
		}

		$migrationId = $migrationClass::id();

		if ( isset( $this->migrations[ $migrationId ] ) ) {
			throw new InvalidArgumentException( 'A migration can only be added once. Make sure there are not id conflicts.' );
		}

		$this->migrations[ $migrationId ] = $migrationClass;
	}

	/**
	 * Helper for adding a bunch of migrations at once
	 *
	 * @since 2.9.0
	 *
	 * @param string[] $migrationClasses
	 */
	public function addMigrations( array $migrationClasses ) {
		foreach ( $migrationClasses as $migrationClass ) {
			$this->addMigration( $migrationClass );
		}
	}
}
