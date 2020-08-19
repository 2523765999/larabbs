<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        $data = parent::toArray($request);
        $data['user'] = new UserResource($this->whenLoaded('user'));
//        $data['user'] = (new UserResource($this->whenLoaded('user')))->showSensitiveFields();
        $data['category'] = new CategoryResource($this->whenLoaded('category'));
        return $data;
    }
}
