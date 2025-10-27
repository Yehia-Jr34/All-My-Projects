<?php

namespace App\Repositories\Eloquent;

use App\Enum\Complaint\ComplaintStatusEnum;
use App\Models\Complaint;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class ComplaintRepository implements ComplaintRepositoryInterface
{

    public function getAll(): array
    {
        return Complaint::where('status', ComplaintStatusEnum::PENDING->value)
            ->get()
            ->toArray();
    }

    public function getById($id): Complaint
    {
        return Complaint::find($id);
    }

    public function create(array $data): Complaint
    {
        return Complaint::create($data);
    }

    public function save(Complaint $complaint): void
    {
        $complaint->save();
    }
}
