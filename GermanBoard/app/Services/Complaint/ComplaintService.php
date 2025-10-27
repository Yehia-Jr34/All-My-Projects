<?php

namespace App\Services\Complaint;

use App\Models\Complaint;
use App\Repositories\Contracts\AnswerComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use Illuminate\Support\Carbon;

class ComplaintService
{
    public function __construct(
        private readonly ComplaintRepositoryInterface $complaintRepository,
        private readonly AnswerComplaintRepositoryInterface $answerComplaintRepository,
    )
    {
    }

    public function create(array $complaint): array
    {
        $user = request()->user();

        $complaint['trainee_id'] = $user->trainee->id;

        return $this->complaintRepository->create($complaint)->toArray();
    }

    public function listAll(): array
    {
        return $this->complaintRepository->getAll();
    }

    public function getById(int $id): array
    {
        $data = $this->complaintRepository->getById($id);
        $data['created_at'] = Carbon::parse($data['created_at'])->format('Y-m-d H:i');
        $data['updated_at'] = Carbon::parse($data['updated_at'])->format('Y-m-d H:i');
        return $data->toArray();
    }

    public function answer(array $answer): array
    {
        $user = request()->user();

        $complaint = $this->complaintRepository->getById($answer['complaint_id']);
        $complaint['status'] = $answer['complaint_status'];
        $this->complaintRepository->save($complaint);

        $answer_data = [
            'user_id' => $user->id,
            'complaint_id' => $complaint->id,
            'answer' => $answer['answer'],
        ];

        return $this->answerComplaintRepository->answer($answer_data)->toArray();
    }
}
