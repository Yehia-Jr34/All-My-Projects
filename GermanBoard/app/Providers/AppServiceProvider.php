<?php

namespace App\Providers;

use App\Repositories\Contracts\AnswerComplaintRepositoryInterface;
use App\Repositories\Contracts\AnswerRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\CodeRepositoryInterface;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\GlobalArticleCategoryRepositoryInterface;
use App\Repositories\Contracts\GlobalArticleRepositoryInterface;
use App\Repositories\Contracts\InternalTrainerActionsRepositoryInterface;
use App\Repositories\Contracts\InternalTrainerRepositoryInterface;
use App\Repositories\Contracts\KeyLearningObjectiveRepositoryInterface;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Repositories\Contracts\PaymentIntentRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PaymentRequestRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Repositories\Contracts\ResetPasswordCodeRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TraineeRepositoryInterface;
use App\Repositories\Contracts\TraineeVideoRepositoryInterface;
use App\Repositories\Contracts\TrainingCategoryRepositoryInterface;
use App\Repositories\Contracts\TrainingRateRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\TrainingSessionsRepositoryInterface;
use App\Repositories\Contracts\TrainingTagRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VideoRepositoryInterface;
use App\Repositories\Eloquent\AnswerComplaintRepository;
use App\Repositories\Eloquent\AnswerRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CertificateRepository;
use App\Repositories\Eloquent\CodeRepository;
use App\Repositories\Eloquent\ComplaintRepository;
use App\Repositories\Eloquent\GlobalArticleCategoryRepository;
use App\Repositories\Eloquent\GlobalArticleRepository;
use App\Repositories\Eloquent\InternalTrainerActionsRepository;
use App\Repositories\Eloquent\InternalTrainerRepository;
use App\Repositories\Eloquent\KeyLearningObjectiveRepository;
use App\Repositories\Eloquent\MembershipRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\NotificationTypeRepository;
use App\Repositories\Eloquent\PaymentIntentRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\PaymentRequestRepository;
use App\Repositories\Eloquent\ProviderRepository;
use App\Repositories\Eloquent\QuestionRepository;
use App\Repositories\Eloquent\QuizRepository;
use App\Repositories\Eloquent\ResetPasswordCodeRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\TagRepository;
use App\Repositories\Eloquent\TraineeRepository;
use App\Repositories\Eloquent\TraineeVideoRepository;
use App\Repositories\Eloquent\TrainingCategoryRepository;
use App\Repositories\Eloquent\TrainingRateRepository;
use App\Repositories\Eloquent\TrainingRepository;
use App\Repositories\Eloquent\TrainingSessionRepository;
use App\Repositories\Eloquent\TrainingTagRepository;
use App\Repositories\Eloquent\TrainingTraineeRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VideoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TraineeRepositoryInterface::class, TraineeRepository::class);
        $this->app->bind(CodeRepositoryInterface::class, CodeRepository::class);
        $this->app->bind(ProviderRepositoryInterface::class, ProviderRepository::class);
        $this->app->bind(ResetPasswordCodeRepositoryInterface::class, ResetPasswordCodeRepository::class);
        $this->app->bind(TrainingRepositoryInterface::class, TrainingRepository::class);
        $this->app->bind(TrainingSessionsRepositoryInterface::class, TrainingSessionRepository::class);
        $this->app->bind(TrainingTraineeRepositoryInterface::class, TrainingTraineeRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(KeyLearningObjectiveRepositoryInterface::class, KeyLearningObjectiveRepository::class);
        $this->app->bind(TrainingCategoryRepositoryInterface::class, TrainingCategoryRepository::class);
        $this->app->bind(PaymentIntentRepositoryInterface::class, PaymentIntentRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(TrainingTagRepositoryInterface::class, TrainingTagRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(AnswerRepositoryInterface::class, AnswerRepository::class);
        $this->app->bind(VideoRepositoryInterface::class, VideoRepository::class);
        $this->app->bind(TraineeVideoRepositoryInterface::class, TraineeVideoRepository::class);
        $this->app->bind(InternalTrainerRepositoryInterface::class, InternalTrainerRepository::class);
        $this->app->bind(MembershipRepositoryInterface::class, MembershipRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(CertificateRepositoryInterface::class, CertificateRepository::class);
        $this->app->bind(NotificationTypeRepositoryInterface::class, NotificationTypeRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(TrainingRateRepositoryInterface::class, TrainingRateRepository::class);
        $this->app->bind(GlobalArticleRepositoryInterface::class, GlobalArticleRepository::class);
        $this->app->bind(GlobalArticleCategoryRepositoryInterface::class, GlobalArticleCategoryRepository::class);
        $this->app->bind(ComplaintRepositoryInterface::class, ComplaintRepository::class);
        $this->app->bind(AnswerComplaintRepositoryInterface::class, AnswerComplaintRepository::class);
        $this->app->bind(InternalTrainerActionsRepositoryInterface::class, InternalTrainerActionsRepository::class);
        $this->app->bind(PaymentRequestRepositoryInterface::class, PaymentRequestRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
