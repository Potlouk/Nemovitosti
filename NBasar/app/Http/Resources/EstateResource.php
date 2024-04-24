<?php

namespace App\Http\Resources;

use App\Models\Estate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstateResource extends JsonResource
{
  
    public function toArray(Request $request): array
    {
        return [
           'location'=> [
                'address' => $this->elocation->address,
                'city' => $this->elocation->city,
                'zip_code' => $this->elocation->zip_code,
                'coordinates' => $this->elocation->coordinates,
             ],
            'energy_consumption' => $this->energy->type,
            'sub_type'=> $this->subType->type,
            'building_material'=> $this->buildingMaterial->type,
            'info'=> $this->info,
            'area'=> $this->area,
            'uuid' => $this->uuid,
            'furniture'=> $this->furniture ,
            'price'=> $this->price,
            'room_type'=> $this->roomType->type ?? null,
            'type'=> $this->etype->type,
            'ownership_type' => $this->ownershipType->type,
            'condition'=> $this->conditionType->type,
            'transaction_type'=> $this->transaction_type,
            'floor'=> $this->floor,
            'reported_count'=> $this->reported_count,
            'equipment'=>  $this->equipment->pluck('name'),
            'user'=> [
                'name'=>  $this->user->name,
                'email'=> $this->user->email,
            ],
            'images' => $this->images,
        ];
    }

    public function show(Estate $estate)
{
    return new EstateResource($estate);
}
}
