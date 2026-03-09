<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    /**
     * Display the audit trail.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%");
            });
        }

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        $activities = $query->paginate(25)->withQueryString();

        // Get unique log names and events for filters
        $logNames = Activity::distinct()->pluck('log_name')->filter();
        $events = Activity::distinct()->pluck('event')->filter();

        return view('audit-trail.index', compact('activities', 'logNames', 'events'));
    }

    /**
     * Display a single activity log.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);

        return view('audit-trail.show', compact('activity'));
    }
}
