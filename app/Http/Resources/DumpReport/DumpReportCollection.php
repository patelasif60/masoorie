<?php

namespace App\Http\Resources\DumpReport;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DumpReportCollection extends ResourceCollection
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