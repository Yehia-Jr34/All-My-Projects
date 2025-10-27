<?php

namespace App\Services\Certificate;

use App\Enum\NotificationTypesEnum;
use App\Jobs\SendNotification;
use App\Models\Certificate;
use App\Models\TrainingTrainee;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    public function __construct(
        private TrainingRepositoryInterface $trainingRepository,
        private CertificateRepositoryInterface $certificateRepository,
        private TrainingTraineeRepositoryInterface $trainingTraineeRepository
    ) {}

    public function unAssignedCertificates(): Collection
    {
        $data = $this->trainingTraineeRepository->unAssignedCertificates();
        return $data;
    }

    public function upload(array $data): void
    {
        if($oldCertificate = $this->certificateRepository->getCertificateByCode($data['code'])){

            throw new \Exception('code already exists',400);

        }

        $trainingTrainee = null;

        if(isset($data['training_trainee_id'])) {

            $trainingTrainee = $this->trainingTraineeRepository->getById($data['training_trainee_id']);

            if (!$trainingTrainee) {
                throw new \Exception('registration not found not found', 400);
            }

            if ($trainingTrainee->certificate ) {
                throw new \Exception('Trainee already has a certificate', 400);
            }
        }

        $certificate = $data['certificate'];

        $filename = $certificate->getClientOriginalName();

        $path = $trainingTrainee
            ? $certificate->storeAs(
                'certificate', // directory
                'training_id_' . $trainingTrainee->training_id . '_' . uniqid() . '_' . $filename, // filename
                'public' // disk
            )
            : $certificate->storeAs(
                'certificate', // directory
                uniqid() . '_' . $filename, // filename
                'public' // disk
            );
        $isUpdated = $this->certificateRepository->create([
            'certification_image' => "storage/$path",
            'training_trainee_id' => $trainingTrainee?->id ?? null ,
            'certification_code' => $data['code'],
            'certification_attached_at' => now(),
        ]);

        if (!$isUpdated) {
            throw new \Exception('Failed to upload certificate');
        }
        $certificateCode = $data['code'];
        if($trainingTrainee) {
            SendNotification::dispatch(
                [$trainingTrainee->trainee->user->id],
                "Certificate Is Ready",
                "Congrats, Your Certificate is Ready, Certificate Code: $certificateCode",
                NotificationTypesEnum::CERTIFICATE_READY
            );
        }
    }

    public function index()
    {
        $data = $this->certificateRepository->index(request()->certificate_code);

        return $data;
    }

//    public function updateCertificate(array $data): void
//    {
//        $trainingTrainee = $this->trainingTraineeRepository->getById($data['training_trainee_id']);
//
//        if (!$trainingTrainee) {
//            throw new \Exception('registration not found');
//        }
//
//        if (isset($data['certificate'])) {
//
//            // Delete old certificate if exists
//            if ($trainingTrainee->certification_image) {
//                Storage::delete('public/' . $trainingTrainee->certification_image);
//            }
//
//            // Handle new certificate upload
//            $certificate = $data['certificate'];
//
//            // Generate secure filename
//            $filename = 'certificate_' . Str::slug(pathinfo($certificate->getClientOriginalName(), PATHINFO_FILENAME))
//                . '_' . time() . '.' . $certificate->extension();
//
//            // Store file with proper path
//            $path = $certificate->storeAs(
//                'certificates/training_' . $trainingTrainee->training_id,
//                $filename,
//                'public'
//            );
//
//            // Save relative path (without 'public/')
//            $data['certification_image'] = str_replace('public/', '', $path);
//        } else {
//            $path = $trainingTrainee->certification_image;
//        }
//
//        $isUpdated = $this->trainingTraineeRepository->updateByTrainingIdAndTraineeId($trainingTrainee->training_id, $trainingTrainee->trainee_id, [
//            'certification_image' => $path,
//            'certification_code' => $data['certification_code'],
//            'certification_attached_at' => now(),
//        ]);
//
//        if (!$isUpdated) {
//            throw new \Exception('Failed to update training trainee');
//        }
//    }

    public function deleteCertificate(int $certificate_id): void
    {
        $certificate = $this->certificateRepository->getById($certificate_id);

        if (!$certificate) {
            throw new \Exception('certificate not found');
        }

        if ($certificate->certification_image) {
            Storage::disk('public')->delete(
                str_replace('storage/', '', $certificate->certification_image)
            );
        }

        $certificate->delete();
    }

    public function byCode(array $data): Certificate | null
    {
        $certificate = $this->certificateRepository->getCertificateByCode($data['code']);

        if (!$certificate) {
            return null;
        }

        return $certificate;
    }
}
