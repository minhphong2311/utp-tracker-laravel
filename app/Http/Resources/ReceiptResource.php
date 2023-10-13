<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'clock_id' => $this->clock_id,
            'image' => $this->image,
            'receipt' => $this->receipt,
            'amount' => floatval($this->amount),
            'status' => $this->status,
        ];
    }
}
