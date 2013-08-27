<?php

interface PropertyCollectorInterface
{
	/**
	 * Return the name of this property.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Return the value of this property.
	 *
	 * @return mixed
	 */
	public function getValue();
}
