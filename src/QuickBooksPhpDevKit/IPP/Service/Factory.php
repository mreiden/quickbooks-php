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
 * @license LICENSE.txt
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 *
 * @package QuickBooks
 * @subpackage IPP
 */

namespace QuickBooksPhpDevKit\IPP\Service;

class Factory
{
	public function newInstance(Context $Context, string $which)
	{
		// Workaround Class being a reserved word in PHP. Class is named Qbclass instead.
		if ($which == 'Class')
		{
			$which = 'Qbclass';
		}
		$class = __NAMESPACE__ . "\\$which";

		return new $class($Context->IPP());
	}
}
