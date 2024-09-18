<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\InfluencerCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Fetch category IDs for the given user
        $influcategory = InfluencerCategory::where('user_id', $this->id)->pluck('category_id');

        $category = Category::where('id',$influcategory)->get();

        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'username' => $this->username,
            'country_id' => $this->country_id,
            'country_name' => $this->country_name,
            'email' => $this->email,
            'image' => $this->image,
            'price_range' => $this->price_range,
            'status' => $this->status,
            'influencer_category' => $category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
