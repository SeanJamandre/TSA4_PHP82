<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class Task extends Model
{
    protected $fillable = ['title', 'description', 'due_date', 'priority', 'status'];
    protected function casts(): array { return ['due_date' => 'date', 'is_archived' => 'boolean']; }
    public function scopeActive(Builder $query): Builder { return $query->where('is_archived', false); }
}
