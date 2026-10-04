<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'preferred_time',
        'message',
    ];

    /**
     * The preferred time slot as shown to people, e.g. "Matin (9h – 12h)".
     */
    public function preferredTimeLabel(): ?string
    {
        $slot = config('site.time_slots.'.$this->preferred_time);

        return $slot ? $slot['label'].' ('.$slot['hours'].')' : null;
    }
}
