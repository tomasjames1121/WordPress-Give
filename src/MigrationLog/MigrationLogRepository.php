<?php

namespace Give\MigrationLog;

use Give\Framework\Database\DB;

/**
 * Class MigrationRepository
 * @package Give\Migration
 *
 * @since 2.9.7
 */
class MigrationLogRepository {

	/**
	 * @var string
	 */
	private $migration_table;

	/**
	 * MigrationRepository constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->migration_table = "{$wpdb->prefix}give_migrations";
	}

	/**
	 * Save Migration
	 *
	 * @param  MigrationLogModel  $model
	 */
	public function save( MigrationLogModel $model ) {
		$query = "
			INSERT INTO {$this->migration_table} (id, status) 
			VALUES (%s, %s) 
			ON DUPLICATE KEY UPDATE
			status = %s,
			last_run = NOW()
		";

		DB::query(
			DB::prepare( $query, $model->getId(), $model->getStatus(), $model->getStatus() )
		);
	}

	/**
	 * Get all migrations
	 *
	 * @return MigrationLogModel[]
	 */
	public function getMigrations() {
		$migrations = [];

		$result = DB::get_results( "SELECT * FROM {$this->migration_table}" );

		if ( $result ) {
			foreach ( $result as $migration ) {
				$migrations[] = MigrationLogFactory::make(
					$migration->id,
					$migration->status,
					$migration->last_run
				);
			}
		}

		return $migrations;
	}

	/**
	 * Get migration by ID
	 *
	 * @param string $id
	 *
	 * @return MigrationLogModel|null
	 */
	public function getMigration( $id ) {
		$migration = DB::get_row(
			DB::prepare( "SELECT * FROM {$this->migration_table} WHERE id = %s", $id )
		);

		if ( $migration ) {
			return MigrationLogFactory::make(
				$migration->id,
				$migration->status,
				$migration->last_run
			);
		}

		return null;
	}

	/**
	 * Get migrations by status
	 *
	 * @param  string  $status
	 *
	 * @return MigrationLogModel[]
	 */
	public function getMigrationsByStatus( $status ) {
		$migrations = [];

		$result = DB::get_results(
			DB::prepare( "SELECT * FROM {$this->migration_table} WHERE status = %s", $status )
		);

		if ( $result ) {
			foreach ( $result as $migration ) {
				$migrations[] = MigrationLogFactory::make(
					$migration->id,
					$migration->status,
					$migration->last_run
				);
			}
		}

		return $migrations;
	}
}
