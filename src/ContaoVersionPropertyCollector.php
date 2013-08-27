<?php

class ContaoVersionPropertyCollector implements PropertyCollectorInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'contao-version';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
		return VERSION . '.' . BUILD;
	}
}
