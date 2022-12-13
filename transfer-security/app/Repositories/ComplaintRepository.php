<?php

namespace App\Repositories;

use App\Models\Complaint;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JetBrains\PhpStorm\ArrayShape;

class ComplaintRepository extends Repository
{
    public function getComplaints(string $username): LengthAwarePaginator
    {
        return Complaint::query()
            ->where('defendant_username', '=', strtolower($username))
            ->orderByDesc('id')
            ->paginate(10);
    }

    #[ArrayShape([
        'total' => 'int',
        'message' => 'string'
    ])]
    public function getComplaintsMessage(string $username): array
    {
        $complaints = $this->getComplaints($username);

        $message = __('commands.check_agent.founded.title');

        /** @var Complaint $complaint */
        foreach ($complaints->items() as $complaint) {
            $message .= "\n\n"
                . __('commands.check_agent.founded.content', [
                    'date' => $complaint->created_at->format('d.m.Y')
                ])
                . "\n"
                . __('commands.check_agent.founded.label')
                . (
                    $complaint->cause_text ?: __('commands.check_agent.founded.not_comment')
                );
        }

        return [
            'total' => $complaints->total(),
            'message' => $message
        ];
    }
}
