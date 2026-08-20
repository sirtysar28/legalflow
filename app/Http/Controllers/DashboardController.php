<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\DocumentAccessRequest;
use App\Models\Application;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if ($user->canReview()) {
            return $this->legalDashboard($user);
        }

        return $this->userDashboard($user);
    }

    private function userDashboard($user)
    {
        Application::syncExpiry();

        $base = Application::mine($user);

        $stats = [
            'total'    => (clone $base)->count(),
            'draft'    => (clone $base)->status(ApplicationStatus::DRAFT)->count(),
            'waiting'  => (clone $base)->whereIn('status', [
                ApplicationStatus::SUBMITTED->value,
                ApplicationStatus::UNDER_REVIEW->value,
                ApplicationStatus::RESUBMITTED->value,
            ])->count(),
            'revision' => (clone $base)->status(ApplicationStatus::REVISION_REQUESTED)->count(),
            'approved' => (clone $base)->status(ApplicationStatus::APPROVED)->count(),
            'rejected' => (clone $base)->status(ApplicationStatus::REJECTED)->count(),
            'expiring' => (clone $base)->expiringSoon((int) config('legalflow.expiring_soon_days', 30))->count(),
        ];

        $recent = Application::mine($user)->with(['permitType', 'department'])
            ->latest()->take(5)->get();

        $expiringSoon = Application::mine($user)->with(['permitType', 'supplier'])
            ->expiringSoon((int) config('legalflow.expiring_soon_days', 30))
            ->orderBy('valid_until')->take(4)->get();

        $myAccessRequests = DocumentAccessRequest::with('application')
            ->where('requested_by', $user->id)->latest()->take(5)->get();

        return view('dashboard.user', compact('stats', 'recent', 'expiringSoon', 'myAccessRequests'));
    }

    private function legalDashboard($user)
    {
        Application::syncExpiry();
        $stats = [
            'submitted' => Application::status(ApplicationStatus::SUBMITTED)->count()
                + Application::status(ApplicationStatus::RESUBMITTED)->count(),
            'review'    => Application::status(ApplicationStatus::UNDER_REVIEW)->count(),
            'approved'  => Application::status(ApplicationStatus::APPROVED)->count(),
            'rejected'  => Application::status(ApplicationStatus::REJECTED)->count(),
            'revision'  => Application::status(ApplicationStatus::REVISION_REQUESTED)->count(),
            'expired'   => Application::status(ApplicationStatus::EXPIRED)->count(),
            'expiring'  => Application::expiringSoon((int) config('legalflow.expiring_soon_days', 30))->count(),
            'access'    => DocumentAccessRequest::where('status', 'ACCESS_REQUESTED')->count(),
        ];

        $queue = Application::with(['user', 'department', 'permitType'])
            ->whereIn('status', [
                ApplicationStatus::SUBMITTED->value,
                ApplicationStatus::RESUBMITTED->value,
                ApplicationStatus::UNDER_REVIEW->value,
            ])->latest('submitted_at')->take(8)->get();

        $accessRequests = DocumentAccessRequest::with(['application', 'requester'])
            ->where('status', 'ACCESS_REQUESTED')->latest()->take(5)->get();

        $expiringSoon = Application::with(['user', 'department', 'permitType', 'supplier'])
            ->expiringSoon((int) config('legalflow.expiring_soon_days', 30))
            ->orderBy('valid_until')->take(4)->get();

        return view('dashboard.legal', compact('stats', 'queue', 'accessRequests', 'expiringSoon'));
    }
}
