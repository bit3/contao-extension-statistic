<?php

class DefaultStatisticData implements StatisticDataInterface
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
    protected $version;

	/**
	 * @var string
	 */
    protected $properties;

	/**
	 * @var array
	 */
	protected $extraData;

	/**
	 * @var bool
	 */
    protected $optional = true;

	/**
	 * @var string
	 */
    protected $notifyUrl;

	function __construct($name, $version = null)
	{
		$this->name        = (string) $name;
		$this->version     = $version;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param string $properties
	 */
	public function setProperties($properties)
	{
		$this->properties = $properties;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @param array $extraData
	 */
	public function setExtraData(array $extraData = null)
	{
		$this->extraData = $extraData;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getExtraData()
	{
		return $this->extraData;
	}

	/**
	 * @param boolean $optional
	 */
	public function setOptional($optional)
	{
		$this->optional = $optional;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isOptional()
	{
		return $this->optional;
	}

	/**
	 * @param string $callback
	 */
	public function setNotifyUrl($callback)
	{
		$this->notifyUrl = $callback;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}
}
