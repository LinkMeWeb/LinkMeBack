<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'nickname' => $this->nickname,
            'foto_path' => $this->foto_path,
            'photos' => $this->photos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
