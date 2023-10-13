<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BreakResource;
use App\Http\Resources\ChangeClockResource;
use App\Http\Resources\ReceiptResource;
use App\Models\Breaks;


class ClockResource extends JsonResource
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
            'name' => $this->getUser->name,
            'event_name' => $this->getEvent->event_name,
            'clockin' => $this->clockin,
            'clockin_photo' => $this->clockin_photo,
            'clockin_location' => $this->clockin_location,
            'clockin_address' => $this->clockin_address,

            'break_list' => BreakResource::collection($this->getBreaks),
            'break' => $this->getTotalBreak(),

            'clockout' => $this->clockout,
            'clockout_photo' => $this->clockout_photo,
            'clockout_location' => $this->clockout_location,
            'clockout_address' => $this->clockout_address,

            'total_time' => $this->total_time,
            'earned_amount' => floatval($this->earned_amount),
            'bonus_pay' => floatval($this->bonus_pay),

            'change_clock' => ChangeClockResource::collection($this->getChangeClock)->first(),

            'notes' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
