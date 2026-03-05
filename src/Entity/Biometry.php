<?php

namespace App\Entity;

readonly class Biometry
{
	public function __construct(
		protected ?Resource $signature = null
	){}

	public function signature(): Resource
	{
		return $this->signature;
	}
}