<?php

namespace App\Providers;

use App\Repositories\Contracts\AddFileNotificationRepositoryInterface;
use App\Repositories\Contracts\AddFileNotificationResponseRepositoryInterface;
use App\Repositories\Contracts\CodeRepositoryInterface;
use App\Repositories\Contracts\FileReportRepositoryInterface;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\FileVersionRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\InvitationNotificationRepositoryInterface;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\InvitationResponseNotificationRepositoryInterface;
use App\Repositories\Contracts\LockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UnlockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use App\Repositories\Contracts\UserImageRepositoryInterface;
use App\Repositories\Contracts\UserReportRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use App\Repositories\Eloquent\AddFileNotificationRepository;
use App\Repositories\Eloquent\AddFileNotificationResponseRepository;
use App\Repositories\Eloquent\CodeRepository;
use App\Repositories\Eloquent\FileReportRepository;
use App\Repositories\Eloquent\FileRepository;
use App\Repositories\Eloquent\FileVersionRepository;
use App\Repositories\Eloquent\GroupRepository;
use App\Repositories\Eloquent\InvitationNotificationRepository;
use App\Repositories\Eloquent\InvitationRepository;
use App\Repositories\Eloquent\InvitationResponseNotificationRepository;
use App\Repositories\Eloquent\LockedFileNotificationRepository;
use App\Repositories\Eloquent\UnlockedFileNotificationRepository;
use App\Repositories\Eloquent\UserGroupRepository;
use App\Repositories\Eloquent\UserImageRepository;
use App\Repositories\Eloquent\UserReportRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\RefreshTokenRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CodeRepositoryInterface::class, CodeRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(InvitationRepositoryInterface::class, InvitationRepository::class);
        $this->app->bind(UserGroupRepositoryInterface::class, UserGroupRepository::class);
        $this->app->bind(UserImageRepositoryInterface::class, UserImageRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(FileVersionRepositoryInterface::class, FileVersionRepository::class);
        $this->app->bind(InvitationNotificationRepositoryInterface::class, InvitationNotificationRepository::class);
        $this->app->bind(InvitationResponseNotificationRepositoryInterface::class, InvitationResponseNotificationRepository::class);
        $this->app->bind(AddFileNotificationRepositoryInterface::class, AddFileNotificationRepository::class);
        $this->app->bind(AddFileNotificationResponseRepositoryInterface::class, AddFileNotificationResponseRepository::class);
        $this->app->bind(LockedFileNotificationRepositoryInterface::class, LockedFileNotificationRepository::class);
        $this->app->bind(UnlockedFileNotificationRepositoryInterface::class, UnlockedFileNotificationRepository::class);
        $this->app->bind(FileReportRepositoryInterface::class, FileReportRepository::class);
        $this->app->bind(UserReportRepositoryInterface::class, UserReportRepository::class);
        $this->app->bind(
            RefreshTokenRepositoryInterface::class,
            RefreshTokenRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
