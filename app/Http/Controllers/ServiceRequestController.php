<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\SupportComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['vehicle', 'requester', 'assignee']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = Auth::user();
        if ($user->role === 'Driver') {
            $query->where('requester_id', $user->id);
        } elseif ($user->role === 'Technician') {
            $query->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('status', 'open');
            });
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get statistics
        $stats = [
            'total' => ServiceRequest::count(),
            'open' => ServiceRequest::where('status', 'open')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'resolved' => ServiceRequest::where('status', 'resolved')->count(),
            'critical' => ServiceRequest::where('priority', 'critical')->count(),
        ];

        return view('support.service-requests.index', compact('serviceRequests', 'stats'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();
        
        return view('support.service-requests.create', compact('vehicles', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'category' => 'required|in:breakdown,maintenance,inspection,other',
            'priority' => 'required|in:low,medium,high,critical',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        DB::beginTransaction();
        try {
            $validated['requester_id'] = Auth::id();
            $validated['status'] = 'open';

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('service-requests', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize()
                    ];
                }
                $validated['attachments'] = $attachments;
            }

            $serviceRequest = ServiceRequest::create($validated);

            // Auto-assign to available technician if critical
            if ($validated['priority'] === 'critical') {
                $technician = User::where('role', 'Technician')
                    ->withCount(['assignedRequests' => function($q) {
                        $q->whereIn('status', ['open', 'in_progress']);
                    }])
                    ->orderBy('assigned_requests_count')
                    ->first();

                if ($technician) {
                    $serviceRequest->update(['assigned_to' => $technician->id]);
                }
            }

            DB::commit();
            return redirect()->route('support.service-requests.show', $serviceRequest)
                ->with('success', 'Service request created successfully. Ticket #' . $serviceRequest->ticket_number);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create service request: ' . $e->getMessage());
        }
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['vehicle', 'requester', 'assignee', 'comments.user']);
        
        // Mark as responded if first view by assigned technician
        if (Auth::id() == $serviceRequest->assigned_to && !$serviceRequest->response_time) {
            $serviceRequest->update(['response_time' => now()]);
        }

        $technicians = User::where('role', 'Technician')->get();
        
        return view('support.service-requests.show', compact('serviceRequest', 'technicians'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:open,in_progress,pending,resolved,closed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'resolution_notes' => 'sometimes|nullable|string'
        ]);

        // Update resolution time if being resolved
        if (isset($validated['status']) && in_array($validated['status'], ['resolved', 'closed'])) {
            $validated['resolution_time'] = now();
        }

        $serviceRequest->update($validated);

        return redirect()->route('support.service-requests.show', $serviceRequest)
            ->with('success', 'Service request updated successfully');
    }

    public function addComment(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_internal'] = $request->has('is_internal') ? true : false;

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('comments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $serviceRequest->comments()->create($validated);

        return back()->with('success', 'Comment added successfully');
    }

    public function assignTechnician(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id'
        ]);

        $serviceRequest->update([
            'assigned_to' => $validated['technician_id'],
            'status' => 'in_progress'
        ]);

        return back()->with('success', 'Technician assigned successfully');
    }

    public function updatePriority(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        $serviceRequest->update(['priority' => $validated['priority']]);

        return back()->with('success', 'Priority updated successfully');
    }

    public function close(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'status' => 'closed',
            'resolution_time' => now()
        ]);

        return back()->with('success', 'Service request closed successfully');
    }

    public function statistics()
    {
        $stats = [
            'by_status' => ServiceRequest::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'by_priority' => ServiceRequest::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get(),
            'by_category' => ServiceRequest::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get(),
            'avg_resolution_time' => ServiceRequest::whereNotNull('resolution_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolution_time)) as avg_hours')
                ->first()->avg_hours,
            'satisfaction_avg' => ServiceRequest::whereNotNull('satisfaction_rating')
                ->avg('satisfaction_rating'),
            'monthly_trend' => ServiceRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get()
        ];

        return view('support.statistics', compact('stats'));
    }
}