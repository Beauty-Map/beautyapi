<?php

namespace App\Trait;

use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\Relation;

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

    public function likedByUsers()
    {
        return $this->likes()->with('user')->get()->pluck('user');
    }

    public static function likedByUser($userId)
    {
        $instance = new static();
        $likeableType = $instance->getMorphAlias();
        return Like::where('user_id', $userId)
            ->where('likeable_type', $likeableType)
            ->with('likeable')
            ->get()
            ->pluck('likeable');
    }

    public function getMorphAlias()
    {
        $morphMap = Relation::morphMap();

        $className = get_class($this);

        $alias = array_search($className, $morphMap);

        return $alias ?: $className;
    }
}
