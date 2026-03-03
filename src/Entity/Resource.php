<?php

namespace App\Entity;

use function rewind, stream_get_contents, mime_content_type;

readonly class Resource
{
	public string $mime;

	public function __construct(
		public mixed $resource
	){
		$this->mime = mime_content_type($resource);
	}

	public function toString(): string
	{
		rewind($this->resource);
		return stream_get_contents($this->resource);
	}
}