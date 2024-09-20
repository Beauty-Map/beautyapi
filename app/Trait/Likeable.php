<?php

namespace App\Trait;

use App\Models\Like;

trait Likeable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // متد برای لایک کردن
    public function like($userId)
    {
        if (!$this->isLikedBy($userId)) {
            return $this->likes()->create(['user_id' => $userId]);
        }
        return null;
    }

    public function unlike($userId)
    {
        return $this->likes()->where('user_id', $userId)->delete();
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function toggleLike($userId)
    {
        if ($this->isLikedBy($userId)) {
            return $this->unlike($userId);
        }
        return $this->like($userId);
    }

    public function getTotalLikes()
    {
        return $this->likes()->count();
    }
}
