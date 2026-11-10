<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];

    public function children() { return $this->hasMany(Menu::class, 'parent_id')->orderBy('urutan'); }

    public static function tops(): array
    {
        return static::whereNull('parent_id')->orderBy('urutan')->pluck('label', 'id')->all();
    }
}
