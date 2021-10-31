<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use function GuzzleHttp\Psr7\_parse_request_uri;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
