<?php

namespace App\Entity;

readonly class Biometry
{
	public function __construct(
		public ?Resource $signature = null
	){}
}