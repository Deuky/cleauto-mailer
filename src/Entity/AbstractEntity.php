<?php

namespace App\Entity;

use ArrayAccess;

abstract readonly class AbstractEntity //implements ArrayAccess
{
	/*public function offsetExists(mixed $offset): bool
	{
		return property_exists($this, $offset);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return $this->{$offset};
	}

	public function offsetSet(mixed $offset, mixed $value)
	{
		throw new \Exception('Only readonly');
	}

	public function offsetUnset(mixed $offset)
	{
		throw new \Exception('Only readonly');
	}*/
}