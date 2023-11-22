<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

class RegisterResource extends JsonResource
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
            'name_fr' => $this->name_fr,
            'sex' => $this->sex,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'age' => $this->age,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'city' => $this->city,
            'district' => $this->district,
            'apartment_number' => $this->apartment_number,
            'building_name' => $this->building_name,
            'password' => $this->password,
            'otp_code'=>$this->otp_code

        ];
    }
}
