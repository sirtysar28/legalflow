<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Audit trail seluruh aktivitas pengajuan.
     */
    public function index(Request $request)
    {
        $histories = ApplicationHistory::with(['application', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q');
                $query->where(fn ($w) => $w->where('action', 'like', "%{$q}%")
                    ->orWhereHas('application', fn ($a) => $a
                        ->where('application_number', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.histories.index', compact('histories'));
    }
}
