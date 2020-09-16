<?php

namespace Give\Framework\Migrations;

use Give\Framework\Migrations\Contracts\Migration;

/**
 * Class MigrationsRunner
 *
 * @since 2.9.0
 */
class MigrationsRunner {
	/**
	 * Option name to store competed migrations.
	 *
	 * @var string
	 */
	private $optionNameToStoreCompletedMigrations = 'give_database_migrations';

	/**
	 * List of completed migrations.
	 *
	 * @since 2.9.0
	 *
	 * @var array
	 */
	private $completedMigrations;

	/**
	 * @since 2.9.0
	 *
	 * @var MigrationsRegister
	 */
	private $migrationRegister;

	/**
	 *  RunMigrations constructor.
	 *
	 * @param MigrationsRegister $migrationRegister
	 */
	public function __construct( MigrationsRegister $migrationRegister ) {
		$this->migrationRegister = $migrationRegister;

		$this->completedMigrations = get_option( $this->optionNameToStoreCompletedMigrations, [] );
	}

	/**
	 * Run database migrations.
	 *
	 * @since 2.9.0
	 */
	public function run() {
		if ( ! $this->hasMigrationToRun() ) {
			return;
		}

		// Store and sort migrations by timestamp
		$migrations = [];

		foreach ( $this->migrationRegister->getMigrations() as $migrationClass ) {
			/* @var Migration $migrationClass */
			$migrations[ $migrationClass::timestamp() ] = $migrationClass;
		}

		ksort( $migrations );

		// Process migrations.
		$newMigrations = [];

		foreach ( $migrations as $migrationClass ) {
			if ( in_array( $migrationClass, $this->completedMigrations, true ) ) {
				continue;
			}

			/** @var Migration $migration */
			$migration = give( $migrationClass );
			$migration->run();

			$newMigrations[] = $migrationClass;
		}

		// Save processed migrations.
		if ( $newMigrations ) {
			update_option(
				$this->optionNameToStoreCompletedMigrations,
				array_merge( $this->completedMigrations, $newMigrations )
			);
		}
	}

	/**
	 * Return whether or not all migrations completed.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	private function hasMigrationToRun() {
		return (bool) array_diff( $this->migrationRegister->getMigrations(), $this->completedMigrations );
	}
}
