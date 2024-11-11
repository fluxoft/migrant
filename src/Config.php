<?php

namespace Fluxoft\Migrant;

use \Fluxoft\Migrant\Exceptions\ConfigException;

class Config implements \ArrayAccess {
	private array $config = [];
	public function __construct($iniFile, $environmentName) {
		if (!is_file($iniFile)) {
			throw new ConfigException(sprintf(
				'No config file was found at "%s". If you have not yet run "migrant init" in this folder, do so now.',
				$iniFile
			));
		}
		$config = parse_ini_file($iniFile, true);
		if (!isset($config[$environmentName])) {
			throw new ConfigException(sprintf(
				'No configuration for the environment "%s" was found. Please check the migrant.ini file.',
				$environmentName
			));
		}
		$this->config = $config[$environmentName];
		foreach (['type', 'host', 'dbname'] as $setting) {
			if (!isset($this->config[$setting])) {
				throw new ConfigException(sprintf(
					'The "%s" must be set for "%s" in the migrant.ini file.',
					$setting,
					$environmentName
				));
			}
		}
	}

	// ArrayAccess implementation
	/**
	 * @inheritDoc
	 */
	public function offsetExists(mixed $offset): bool {
		return (isset($this->config[$offset]));
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet(mixed $offset): mixed {
		return $this->config[$offset];
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new ConfigException('Config values cannot be written by this class.');
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset(mixed $offset): void {
		throw new ConfigException('Config values cannot be deleted by this class.');
	}
}
