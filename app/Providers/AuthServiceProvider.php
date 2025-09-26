<?php

namespace App\Providers;

use App\Models\ThreadComment;
use App\Policies\ThreadCommentPolicy;
use App\Models\Thread;
use App\Policies\ThreadPolicy;
use App\Models\PostComment;
use App\Policies\PostCommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ThreadComment::class => ThreadCommentPolicy::class,
        PostComment::class => PostCommentPolicy::class,
        Thread::class => ThreadPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // additional gates can be defined here
    }
}
