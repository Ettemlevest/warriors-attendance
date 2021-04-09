<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
            'created_at' => (string) $this->pivot->created_at,
            'extra' => (bool) $this->pivot->extra,
            'attended' => (bool) $this->pivot->attended,
        ];
    }
}
