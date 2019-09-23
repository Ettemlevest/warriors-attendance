<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'photo' => $this->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
            'pivot' => $this->whenLoaded('pivot'),
        ];
    }
}
