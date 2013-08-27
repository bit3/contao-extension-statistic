<?php

interface ExtensionVersionDetectorInterface
{
	/**
	 * Detect the version of a specific extension.
	 * 
	 * @param string $extensionName The extension name.
	 *
	 * @return null|string The version or null, if the extension could not be found.
	 */
	public function detectVersion($extensionName);
}
