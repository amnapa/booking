<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','slug','description'];

    public function rules()
    {
        return [
            'name' => 'required',
            'slug' => "required|unique:hotels,slug, {$this->id}",
            'description' => 'required',
        ];
    }

    //create slug based on user input
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    //Define One to Many relationship between Hotel and Room Models
    public function room()
    {
        return $this->hasMany(Room::class);
    }

    //Find hotel by given name
    public function scopeName($query, $name)
    {
        if($name)
            $query->where('name', 'LIKE', '%' . $name . '%');
    }

    //Find hotel by given slug
    public function scopeSlug($query, $slug)
    {
        if($slug)
            $query->where('slug', 'LIKE', '%' . $slug . '%');
    }
}
