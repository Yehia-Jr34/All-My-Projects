<?php

use App\Http\Controllers\API\V1\Admin\AdminInternalTrainersController;
use App\Http\Controllers\API\V1\Admin\AdminTrainingController;
use App\Http\Controllers\API\V1\Agora\AgoraController;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Balance\BalanceController;
use App\Http\Controllers\API\V1\Balance\PaymentRequestController;
use App\Http\Controllers\API\V1\Blog\BlogController;
use App\Http\Controllers\API\V1\Category\CategoryController;
use App\Http\Controllers\API\V1\Auth\GoogleController;
use App\Http\Controllers\API\V1\Certificate\CertificateController;
use App\Http\Controllers\API\V1\Complaint\ComplaintController;
use App\Http\Controllers\API\V1\InternalTrainer\InternalTrainerActionController;
use App\Http\Controllers\API\V1\InternalTrainer\InternalTrainerController;
use App\Http\Controllers\API\V1\InternalTrainer\InternalTrainerTrainingController;
use App\Http\Controllers\API\V1\Membership\MembershipController;
use App\Http\Controllers\API\V1\Notification\NotificationController;
use App\Http\Controllers\API\V1\Provider\ProviderController;
use App\Http\Controllers\API\V1\Provider\ProviderInternalTrainers;
use App\Http\Controllers\API\V1\Provider\ProviderTrainingController;
use App\Http\Controllers\API\V1\Quiz\Question\QuizQuestionController;
use App\Http\Controllers\API\V1\Quiz\QuizController;
use App\Http\Controllers\API\V1\Rating\RatingController;
use App\Http\Controllers\API\V1\Search\SearchController;
use App\Http\Controllers\API\V1\Tag\TagController;
use App\Http\Controllers\API\V1\Trainee\TraineeController;
use App\Http\Controllers\API\V1\Training\LiveTrainingController;
use App\Http\Controllers\API\V1\Training\OnsiteTrainingController;
use App\Http\Controllers\API\V1\Training\RecordedTrainingController;
use App\Http\Controllers\API\V1\Training\TraineeTrainingController;
use App\Http\Controllers\API\V1\Training\TrainingController;
use App\Http\Controllers\API\V1\TrainingSessions\TrainingSessionController;
use App\Http\Controllers\API\V1\Video\VideoController;
use App\Http\Controllers\API\V1\Webhooks\StripeWebhookController;
use App\Http\Middleware\CanHaveInternalTrainers;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\CanManageOnsiteTrainings;
use App\Http\Middleware\EnsureProvider;
use App\Http\Middleware\EnsureTrainee;
use App\Http\Middleware\GlobalErrorHandler;
use App\Http\Middleware\MembershipInvalidMiddleware;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::prefix('v1')->middleware([GlobalErrorHandler::class])->group(function () {

    // Trainee
    Route::post('auth/askForOTP', [AuthController::class, 'askForOTP']);
    Route::post('verifyAccount', [AuthController::class, 'verifyAccount']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('google/checkEmailExistence', [AuthController::class, 'checkEmailExistence']);
    Route::post('google/register', [AuthController::class, 'register_with_google']);

    Route::post('trainee/auth/forgetPassword', [AuthController::class, 'forgetPasswordForTrainee']);
    Route::post('trainee/auth/resendForgetPasswordCode', [AuthController::class, 'resendForgetPasswordCode']);
    Route::post('trainee/auth/checkCode', [AuthController::class, 'checkCodeForTrainee']);
    Route::post('trainee/auth/resendOTP', [AuthController::class, 'resendVerificationCode']);
    Route::get('auth/google', [GoogleController::class, function () {
        return Socialite::driver('google')->redirect();
    }]);

    // Provider
    Route::post('auth/provider/login', [AuthController::class, 'provider_login']);
    Route::post('provider/auth/forgetPassword', [AuthController::class, 'forgetPasswordForProvider']);
    Route::post('provider/auth/checkCode', [AuthController::class, 'checkCodeForProvider']);
    Route::get('trainee/provider/{id}', [ProviderController::class, 'getProviderForTrainee']);

    // Shared
    Route::post('auth/login', [AuthController::class, 'login']);

    // Trainings
    Route::get('trainings/list', [TrainingController::class, 'listTrainings']);
    Route::get('training/show/{id}', [TrainingController::class, 'showTraining']);

    // Articles
    Route::get('articles', [BlogController::class, 'getAll']);
    Route::get('article/{id}', [BlogController::class, 'getById']);

});

Route::prefix('v1')->middleware([GlobalErrorHandler::class, 'auth:sanctum'])->group(function () {

    // Trainee
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::get('home', [TrainingController::class, 'getDataForHomePage']);

    Route::post('trainee/payment/checkResult', [TrainingController::class, 'payment_response']);

    //live
    Route::post('trainee/training/joinSession', [TrainingSessionController::class, 'joinSession']);

    //recorded
    Route::get('trainee/training/getVideosAndExams/{training_id}', [TrainingController::class, 'getVideosAndExams']);
    Route::post('trainee/training/video/watched/{video_id}', [TrainingController::class, 'watchedVideo']);
    Route::get('training/recorded/{id}', [RecordedTrainingController::class, 'showTraineeTraining']);
    Route::middleware([EnsureTrainee::class])->group(function () {

        Route::get('trainee/training/by-category/{category_id}', [TrainingController::class, 'byCategory']);

        Route::get('trainee/training', [TraineeTrainingController::class, 'index']);

        Route::get('training/live/{id}', [LiveTrainingController::class, 'show']);


        Route::post('trainee/rating', [RatingController::class, 'store']);

        Route::post('trainee/quiz/submit', [QuizController::class, 'submit']);

        Route::get('trainee/quiz/{quiz_id}', [QuizController::class, 'showForTrainee']);

        Route::post('trainee/quiz/question/check', [QuizQuestionController::class, 'checkAnswer']);

        Route::post('trainee/ensure_enrolled', [TraineeController::class, 'ensure_enrolled']);

        Route::get('trainee/blogs/getAll', [BlogController::class, 'getAll']);

        Route::get('trainee/blogs/{blog_id}', [BlogController::class, 'getById']);

        Route::post('trainee/complaint/add', [ComplaintController::class, 'create']);

        //Notification
        Route::get('notification/me', [NotificationController::class, 'getMyProviderNotification']);
        Route::get('notification/is_unread', [NotificationController::class, 'isThereUnreadNotifications']);

        Route::get('trainee/provider/{id}', [ProviderController::class, 'getProviderForTrainee']);

        // Enroll
        Route::post('trainee/training/enroll', [TrainingController::class, 'enroll']);


    });

    Route::post('provider/training/makeTraineePassTheTraining', [TrainingController::class, 'makeTraineePassTheTraining']);

    //Internal Trainer
    Route::middleware([CanHaveInternalTrainers::class])->group(function () {
        Route::post('provider/internal-trainer', [InternalTrainerController::class, 'create']);
        Route::get('provider/internal-trainer', [InternalTrainerController::class, 'index']);
        Route::get('/provider/internal-trainer/training-assigned/{id}', [InternalTrainerTrainingController::class, 'index']);
        Route::post('/provider/internal-trainer/training-assigned', [InternalTrainerTrainingController::class, 'assign']);
        Route::get('provider/internal-trainer/actions/{id}', [InternalTrainerActionController::class, 'index']);
    });

    //admin
    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::post('admin/provider/create', [ProviderController::class, 'create']);
        Route::get('admin/provider', [ProviderController::class, 'index']);
        Route::get('admin/provider/{provider_id}', [ProviderController::class, 'show']);

        Route::post('admin/membership/add', [MembershipController::class, 'create']);

        Route::get('admin/trainee/noCertificate/{training_id}', [CertificateController::class, 'noCertificate']);

        Route::get('admin/trainings/provider/{id}', [ProviderTrainingController::class, 'index']);
        Route::get('admin/trainings/provider/trainee/{training_id}', [ProviderTrainingController::class, 'get_trainees']);
        Route::get('admin/training/isAdminTraining/{training_id}', [AdminTrainingController::class, 'isAdminTraining']);
        Route::get('admin/provider/internal-trainers/{provider_id}', [ProviderInternalTrainers::class, 'index']);
        Route::get('admin/internal-trainer/isAdminInternalTrainer/{internal_trainer_id}', [AdminInternalTrainersController::class, 'isAdminInternalTrainer']);

        Route::patch('admin/membership/invoke/{membership_id}', [MembershipController::class, 'invoke']);
        Route::delete('admin/membership/{membership_id}', [MembershipController::class, 'destroy']);
        Route::patch('admin/membership/update/{membership_id}', [MembershipController::class, 'update']);

        Route::get('admin/trainee', [TraineeController::class, 'index']);
        Route::get('admin/trainee/{trainee_id}', [TraineeController::class, 'show']);

        Route::get('admin/certificates/all', [CertificateController::class, 'index']);
        Route::get('admin/certificates/unassigned', [CertificateController::class, 'unAssignedCertificates']);
        Route::post('admin/certificate/upload', [CertificateController::class, 'upload']);
        Route::post('admin/certificate/update', [CertificateController::class, 'update']);
        Route::delete('admin/certificate/delete/{training_trainee_id}', [CertificateController::class, 'delete']);

        Route::get('admin/complaint/listAll', [ComplaintController::class, 'listAll']);
        Route::get('admin/complaint/get/{complaint_id}', [ComplaintController::class, 'getById']);
        Route::post('admin/complaint/answer', [ComplaintController::class, 'answer']);

        Route::get('admin/article/all', [BlogController::class, 'adminAll']);
        Route::patch('admin/article/reject', [BlogController::class, 'reject']);
        Route::patch('admin/article/accept/{id}', [BlogController::class, 'accept']);
        Route::get('admin/article/provider/{id}', [BlogController::class, 'byProvider']);

        // Balance
        Route::get('admin/payment-request/all', [PaymentRequestController::class, 'all']);
        Route::patch('admin/payment-request/approve/{id}', [PaymentRequestController::class, 'approve']);
        Route::get('admin/balance/summary', [PaymentRequestController::class, 'getAdminSummary']);

        //Category
        Route::get('admin/category', [CategoryController::class, 'getAdmin']);
        Route::post('admin/category', [CategoryController::class, 'create']);
        Route::patch('admin/category', [CategoryController::class, 'update']);



    });

    Route::middleware([CanManageOnsiteTrainings::class])->group(function () {
        Route::get('provider/training/onsite', [OnsiteTrainingController::class, 'index']);
        Route::get('provider/training/onsite/{id}', [OnsiteTrainingController::class, 'show']);
    });

    Route::get('/provider/training/live/titles', [TrainingController::class, 'getTitles']);

    Route::post('provider/training/addSessions', [TrainingController::class, 'addSessions'])->middleware(MembershipInvalidMiddleware::class);
    Route::post('provider/training/editSession', [TrainingController::class, 'editSessionDatesAndTimes'])->middleware(MembershipInvalidMiddleware::class);
    Route::post('provider/training/editSessionStatus', [TrainingController::class, 'editSessionStatus'])->middleware(MembershipInvalidMiddleware::class);

    Route::post('provider/training/addAttachments', [TrainingController::class, 'addAttachments'])->middleware(MembershipInvalidMiddleware::class);
    Route::get('provider/training/getLiveTrainings', [TrainingController::class, 'getLiveTrainings']);

    // live training
    Route::get('provider/training/live/{training_id}', [TrainingController::class, 'getLiveTrainingDetailsForProvider']);

    //Recorded
    Route::post('provider/training/recorded/video', [VideoController::class, 'create']);
    Route::post('provider/training/recorded/video/edit', [VideoController::class, 'update']);
    Route::get('provider/training/recorded/video/getTitles/{training_id}', [VideoController::class, 'getTitles']);
    Route::post('provider/training/recorded/quiz', [QuizController::class, 'create']);
    Route::post('provider/training/recorded/quiz/edit', [TrainingController::class, 'edit_exam']);


    Route::get('provider/category', [CategoryController::class, 'get']);
    Route::get('provider/tags', [TagController::class, 'get']);

    // Shared
    Route::post('training/getTraining', [TrainingController::class, 'getTraining']);
    Route::post('training/getTrainingDetails', [TrainingController::class, 'getTrainingDetails']); // live
    Route::post('auth/resetPassword', [AuthController::class, 'resetPassword']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('all/search', [SearchController::class, 'search']);
    Route::post('all/addView', [BlogController::class, 'addView']);

    // agora
    Route::get('agora/token/channel/{trainingSessionId}', [AgoraController::class, 'generateToken']);

    //Notification
    Route::get('provider/notification/me', [NotificationController::class, 'getMyProviderNotification']);
    Route::get('provider/notification/is_un_read', [NotificationController::class, 'isThereUnreadNotifications']);

    Route::middleware([EnsureProvider::class])->group(function () {

        //Balance
        Route::get('provider/balance/details', [BalanceController::class, 'getMyBalance']);
        Route::post('provider/payment-request', [PaymentRequestController::class, 'create']);
        Route::get('provider/payment-request/me', [PaymentRequestController::class, 'getMy']);
        Route::get('provider/balance/summary', [PaymentRequestController::class, 'getSummary']);


        //questions
        Route::post('provider/training/quiz/question', [QuizQuestionController::class, 'create']);
        Route::patch('provider/training/quiz/question', [QuizQuestionController::class, 'edit']);

        // Recorded Training
        Route::get('provider/training/recorded/{id}', [RecordedTrainingController::class, 'showWithQuizzes']);
        Route::get('provider/training/recorded', [RecordedTrainingController::class, 'index']);


        Route::post('provider/info/complete', [ProviderController::class, 'complete']);
        Route::get('/training/recorded/quiz/{quiz_id}', [QuizController::class, 'show']);

        // Article
        Route::post('provider/article/edit', [BlogController::class, 'editArticle']);
        Route::delete('provider/article/delete/{article_id}', [BlogController::class, 'deleteArticle']);
        Route::get('provider/article/me', [BlogController::class, 'getMyArticle']);
        Route::post('provider/article/add', [BlogController::class, 'addArticle']);
        Route::get('provider/article/{id}', [BlogController::class, 'show']);

        Route::post('provider/training/addTraining', [TrainingController::class, 'create'])->middleware(MembershipInvalidMiddleware::class);
        Route::patch('provider/training/edit', [TrainingController::class, 'updateTraining'])->middleware(MembershipInvalidMiddleware::class);
        Route::post('provider/training/edit/cover', [TrainingController::class, 'updateTrainingCover'])->middleware(MembershipInvalidMiddleware::class);
    });


});

Route::post('/v1/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

Route::post('/v1/certificate/by-code', [CertificateController::class, 'byCode']);

// Route::POST('/v1/test', function () {
//     SendNotification::dispatch(
//         [1],
//         'Test Notification',
//         'This is a test notification',
//         NotificationTypesEnum::ENROLLED_IN_TRAINING,
//         null,
//     );
// });
