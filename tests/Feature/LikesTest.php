<?php

use App\Models\User;
use App\Models\Post;

it('a user can like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);
    
    $post->like();

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => get_class($post)
    ]);

});