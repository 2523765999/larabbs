<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
        return [
            'id' => $this->id,
            'user_id' => (int) $this->user_id,
            'topic_id' => (int) $this->topic_id,
            'content' => $this->content,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,//"updated_at": "2020-08-21 14:50:09"
//            'updated_at' => $this->updated_at,
//            ↓
//            "updated_at": {
//                "date": "2020-08-21 14:41:10.000000",
//                "timezone_type": 3,
//                "timezone": "Asia/Shanghai"
//            }
        ];
    }
}
