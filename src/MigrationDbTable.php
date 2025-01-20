<?php

namespace Fluxoft\Migrant;

use PDO;
use PDOException;

class MigrationDbTable {
	private readonly PDO $connection;
	private readonly string $tableName;

	public function __construct(PDO $connection, string $tableName = 'migrant_log') {
		$this->connection = $connection;
		$this->tableName = $tableName;
		$this->init();
	}

	public function GetExecutedMigrations(): array {
		$sql = "SELECT revision, migration_start, migration_end FROM {$this->tableName} ORDER BY revision";
		$stmt = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
	}

	public function AddMigration(int $revision, \DateTime $startTime, \DateTime $endTime): void {
		$sql = "INSERT INTO {$this->tableName} (revision, migration_start, migration_end)
		        VALUES (:revision, :migrationStart, :migrationEnd)";
		$stmt = $this->connection->prepare($sql);
		$stmt->execute([
			'revision' => $revision,
			'migrationStart' => $startTime->format('Y-m-d H:i:s'),
			'migrationEnd' => $endTime->format('Y-m-d H:i:s')
		]);
	}

	public function RemoveMigration(int $revision): void {
		$sql = "DELETE FROM {$this->tableName} WHERE revision = :revision";
		$stmt = $this->connection->prepare($sql);
		$stmt->execute(['revision' => $revision]);
	}

	protected function init(): void {
		if (!$this->doesTableExist()) {
			$this->createMigrationTable();
		}
	}

	protected function doesTableExist(): bool {
		try {
			$this->connection->query("SELECT 1 FROM {$this->tableName} LIMIT 1");
			return true;
		} catch (PDOException $e) {
			return false;
		}
	}

	protected function createMigrationTable(): void {
		$dbType = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
		$datetimeType = match ($dbType) {
			'pgsql' => 'TIMESTAMP',
			'mysql' => 'DATETIME',
			'sqlite' => 'TEXT',
			default => 'TIMESTAMP',
		};

		$sql = "CREATE TABLE {$this->tableName} (
			revision BIGINT NOT NULL PRIMARY KEY,
			migration_start {$datetimeType} NOT NULL,
			migration_end {$datetimeType} NOT NULL
		)";
		$this->connection->exec($sql);
	}
}
