<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
	public $status;
	public $code;
	public $message;
	public $resource;

	public function __construct($status, $code, $message, $resource)
	{
		parent::__construct($resource);
		$this->status = $status;
		$this->code = $code;
		$this->message = $message;
	}
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'status' => $this->status,
			'code' => $this->code,
			'message' => $this->message,
			'data' => $this->resource
		];
	}
}
