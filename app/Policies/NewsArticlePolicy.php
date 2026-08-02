<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\NewsArticle;
use App\Models\User;

class NewsArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_news::article');
    }

    public function view(User $user, NewsArticle $newsArticle): bool
    {
        return $user->can('view_news::article');
    }

    public function create(User $user): bool
    {
        return $user->can('create_news::article');
    }

    public function update(User $user, NewsArticle $newsArticle): bool
    {
        return $user->can('update_news::article');
    }

    public function delete(User $user, NewsArticle $newsArticle): bool
    {
        return $user->can('delete_news::article');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_news::article');
    }

    public function forceDelete(User $user, NewsArticle $newsArticle): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, NewsArticle $newsArticle): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, NewsArticle $newsArticle): bool
    {
        return $user->can('replicate_news::article');
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
