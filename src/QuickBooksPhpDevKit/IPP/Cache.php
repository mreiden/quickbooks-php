<?php declare(strict_types=1);

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 *
 *
 */

namespace QuickBooksPhpDevKit\IPP;

class Cache
{
	public const MAP_QBXML = 'qbxml';

	public const MAP_IPP = 'ipp';

	protected $_context;

	public function __construct($Context, $dsn, string $map = static::MAP_IPP, $map_dsn)
	{
		$this->_context = $Context;
	}

	protected function _mapFactory(string $map)
	{
		$class = __NAMESPACE__ . "\\Cache\\Mapper\\" . ucfirst(strtolower($map));
		return new $class($map_dsn);
	}

	public function refresh(array $resources = [], ?array $IDs = null)
	{

	}

	public function add(array $resources = [], ?array $IDs = null)
	{

	}

	public function mod(array $resources = [], ?array $IDs = null)
	{

	}

	public function query(array $resources = [], ?array $IDs = null)
	{

	}

	public function delete(array $resources = [], array $IDs = null)
	{

	}

	public function todo(array $resources = [], array $actions = [])
	{
		foreach ($resources as $resource)
		{
			foreach ($actions as $action)
			{
				//$todos =


			}
		}
	}

	public function initialized()
	{

	}

	public function initialize()
	{

	}
}
