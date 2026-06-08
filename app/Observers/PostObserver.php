<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function created(Post $post): void
    {
        Post::clearCountCache();
    }

    public function updated(Post $post): void
    {
        Post::clearCountCache();
    }

    public function deleted(Post $post): void
    {       
        Post::clearCountCache();
    }

    public function restored(Post $post): void
    {
        Post::clearCountCache();
    }

    public function forceDeleted(Post $post): void
    {
        Post::clearCountCache();
    }
}
