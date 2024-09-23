<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'type',
        'is_read',
        'actor_id', // Add this line
    ];
    // Define a relationship to the User model for the user who receives the notification
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // The user who receives the notification
    }

    // Define a relationship to the User model for the user who triggered the action
    public function actor()
{
    return $this->belongsTo(User::class, 'actor_id'); // Assuming actor_id is the field
}

}
