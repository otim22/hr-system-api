<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'surname' => $this->surname,
            'other_names' => $this->other_names,
            'date_of_birth' => $this->date_of_birth,
            'unique_code' => $this->unique_code,
            'employee_number' => $this->employee_number,
            'is_verified' => $this->is_verified,
            'image_src' => $this->image_src,
        ];
    }
}
