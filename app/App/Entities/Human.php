<?php
namespace App\Entities;

use Rico\Dashboard\Entities\Entity;

class Human extends Entity
{
    protected $table = 'humans';
    protected $fillable = ['title', 'bio', 'image', 'height', 'location', 'birthdate'];

    public function pets()
    {
        return $this->hasMany('\App\Entities\Pet');
    }
}
