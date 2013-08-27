<?php

interface StatisticDataInterface
{
	/**
	 * Return the extension name.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Return the extension version.
	 *
	 * @return string
	 */
	public function getVersion();

	/**
	 * Return the list of properties that should be included into the statistic data.
	 * @return string
	 */
	public function getProperties();

	/**
	 * Return a set of extra data included into the statistic data.
	 *
	 * @return mixed
	 */
	public function getExtraData();

	/**
	 * Return true if this statistic data is optional to be processes.
	 *
	 * @return boolean
	 */
	public function isOptional();

	/**
	 * Return the url to push this data.
	 *
	 * @return string
	 */
	public function getNotifyUrl();
}