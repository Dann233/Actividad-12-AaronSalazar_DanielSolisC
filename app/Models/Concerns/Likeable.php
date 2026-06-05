<?php

namespace App\Models\Concerns;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;

trait Likeable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(): void
    {
        $this->likes()->create([
            'user_id' => Auth::id(),
        ]);
    }
}