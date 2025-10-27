<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $nameField = 'name_'.$locale;

        return [
            'id' => $this->id,
            'name' => $this->$nameField,
            'number_of_courses' => $this->training_categories->count(),
        ];
    }
}
