<?php

namespace Fluxoft\Migrant;

class Setup {
	// Static wrapper for Composer's post-install script
	public static function runCopy() {
		echo "Running post-install setup...\n"; // debugging output
		$instance = new self();
		$instance->copyToProjectRoot();
	}

	// Main logic, kept non-static for testability
	public function copyToProjectRoot() {
		$destinationPath = getcwd() . '/migrant';

		// Content for the new root-level 'migrant' script
		$scriptContent = <<<'PHP'
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/bin/migrant';
PHP;

		// Write the wrapper script to the project root
		file_put_contents($destinationPath, $scriptContent);
		echo "Root-level 'migrant' script created successfully.\n";

		// Attempt to make the script executable
		if (!chmod($destinationPath, 0755)) {
			echo "Warning: Could not make 'migrant' executable. Please check permissions.\n";
		}
	}
}
