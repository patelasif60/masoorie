<?php

namespace App\Http\Resources\TourWiseTourists;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TourWiseTouristsCollection extends ResourceCollection
{
	/**
	 * Transform the resource collection into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'data' => $this->collection,
		];
	}
}