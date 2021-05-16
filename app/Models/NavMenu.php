<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavMenu extends Model
{
    use HasFactory;

    /**
     * the attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'type',
        'slug',
        'sequence',
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 5;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'link',
        'type_full_string'
    ];

    /**
     * get full link of this menu
     *
     * @return string
     */
    public function getLinkAttribute(): string
    {
        $url = config('app.url');
        return "{$url}/{$this->slug}";
    }

    /**
     * get full type string of this menu
     *
     * @return string
     */
    public function getTypeFullStringAttribute(): string
    {
        return \ucwords("{$this->type} navigation bar");
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
