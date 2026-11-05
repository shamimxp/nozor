<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\UserActivity;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserActivityController extends Controller
{
    use AuthorizesAdminModule;

    public function __construct()
    {
        $this->authorizeAdminModule('user_activity');
    }

    /**
     * Display a listing of user activities with real-time last action & inactivity status.
     * Corresponds to "/company/user-activities" in SAL.
     */
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        if ($request->ajax()) {
            $now = Carbon::now();

            // 1. Gather internal staff users (Admins and Workers)
            $usersCollection = collect();

            // Admins / Staff
            $admins = Admin::all();
            foreach ($admins as $adm) {
                $usersCollection->push([
                    'id' => $adm->id,
                    'name' => $adm->name ?? 'Admin #' . $adm->id,
                    'email' => $adm->email ?? '-',
                    'phone' => $adm->phone ?? '-',
                    'role' => 'admin',
                    'role_label' => 'Admin / Staff',
                    'role_bg' => '#fef3c7',
                    'role_color' => '#b45309',
                    'role_border' => '#fde68a',
                ]);
            }

            // Factory Workers / Staff
            $workers = Worker::all();
            foreach ($workers as $wkr) {
                $usersCollection->push([
                    'id' => $wkr->id,
                    'name' => $wkr->name ?? 'Worker #' . $wkr->id,
                    'email' => $wkr->email ?? '-',
                    'phone' => $wkr->phone ?? '-',
                    'role' => 'worker',
                    'role_label' => 'Factory Worker',
                    'role_bg' => '#f1f5f9',
                    'role_color' => '#475569',
                    'role_border' => '#cbd5e1',
                ]);
            }

            // Filter by specific role
            if ($request->filled('role') && $request->role !== 'all') {
                $usersCollection = $usersCollection->where('role', $request->role)->values();
            }

            // Filter by specific user
            if ($request->filled('user_id')) {
                $uid = (int) $request->user_id;
                $usersCollection = $usersCollection->where('id', $uid)->values();
            }

            // Filter by keyword
            if ($request->filled('search_keyword')) {
                $kw = strtolower(trim($request->search_keyword));
                $usersCollection = $usersCollection->filter(function ($item) use ($kw) {
                    return str_contains(strtolower($item['name']), $kw)
                        || str_contains(strtolower($item['email']), $kw)
                        || str_contains(strtolower($item['phone']), $kw);
                })->values();
            }

            // 2. Fetch all activities for the selected date grouped by user_role and user_id
            $dayActivities = UserActivity::where('date', $selectedDate)
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->user_role . '_' . $item->user_id;
                });

            // 3. Map metrics and inactivity status for each user
            $data = $usersCollection->map(function ($u) use ($dayActivities, $now, $selectedDate) {
                $key = $u['role'] . '_' . $u['id'];
                $userActs = $dayActivities->get($key, collect());
                $totalActs = $userActs->count();
                $lastActivity = $userActs->first();

                $idleMinutes = null;
                $lastActionTimeFormatted = '-';
                $lastActionRelative = '-';
                $lastActionDescription = 'No activity recorded yet today';
                $lastActionType = 'none';
                $lastActionIp = null;

                if ($lastActivity) {
                    $actTime = $lastActivity->date_time ?: ($lastActivity->created_at ?: Carbon::parse($lastActivity->date . ' ' . $lastActivity->time));
                    $idleMinutes = $actTime->diffInMinutes($now);
                    $lastActionTimeFormatted = $actTime->format('h:i A');
                    $lastActionRelative = $actTime->diffForHumans();
                    $lastActionDescription = $lastActivity->description;
                    $lastActionType = $lastActivity->action_type ?: 'activity';
                    $lastActionIp = $lastActivity->ip_address;
                }

                // Status classification
                if ($totalActs === 0) {
                    $statusKey = 'no_activity';
                    $statusBadge = '<span class="badge badge-light-secondary badge-pill" style="font-size: 11px; padding: 4px 8px; font-weight: 600;">No Activity Today</span>';
                } elseif ($idleMinutes < 30) {
                    $statusKey = 'active';
                    $statusBadge = '<span class="badge badge-light-success badge-pill font-weight-bold" style="font-size: 11px; padding: 4px 8px; background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;"><i data-feather="check-circle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Active (' . $this->formatMinutes($idleMinutes) . ')</span>';
                } elseif ($idleMinutes < 60) {
                    $statusKey = 'idle';
                    $statusBadge = '<span class="badge badge-light-warning badge-pill font-weight-bold" style="font-size: 11px; padding: 4px 8px; background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a;"><i data-feather="clock" style="width: 12px; height: 12px; margin-right: 4px;"></i> Idle (' . $this->formatMinutes($idleMinutes) . ')</span>';
                } else {
                    $statusKey = 'alert';
                    $statusBadge = '<span class="badge badge-light-danger badge-pill font-weight-bold" style="font-size: 11px; padding: 4px 8px; background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"><i data-feather="alert-triangle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Inactive (' . $this->formatMinutes($idleMinutes) . ')</span>';
                }

                return [
                    'id' => $u['id'],
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'phone' => $u['phone'],
                    'role' => $u['role'],
                    'role_label' => $u['role_label'],
                    'role_bg' => $u['role_bg'],
                    'role_color' => $u['role_color'],
                    'role_border' => $u['role_border'],
                    'total_activities' => $totalActs,
                    'last_action_desc' => $lastActionDescription,
                    'last_action_time' => $lastActionTimeFormatted,
                    'last_action_relative' => $lastActionRelative,
                    'last_action_type' => $lastActionType,
                    'last_action_ip' => $lastActionIp,
                    'idle_minutes' => $idleMinutes,
                    'status_key' => $statusKey,
                    'status_badge' => $statusBadge,
                ];
            });

            // Filter by inactivity status
            if ($request->filled('status') && $request->status !== 'all') {
                $data = $data->where('status_key', $request->status)->values();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_info', function ($row) {
                    $avatar = strtoupper(substr($row['name'] ?? 'U', 0, 1));
                    return '
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-1" style="width: 36px; height: 36px; border-radius: 50%; background: #111C38; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px; flex-shrink: 0;">
                                ' . $avatar . '
                            </div>
                            <div>
                                <span class="font-weight-bold text-dark d-block" style="font-size: 13.5px;">' . e($row['name']) . '</span>
                                <small class="text-muted d-block font-small-2">' . e($row['phone']) . ' &bull; ' . e($row['email']) . '</small>
                            </div>
                        </div>';
                })
                ->addColumn('role_badge', function ($row) {
                    return '<span class="badge" style="background-color: ' . $row['role_bg'] . '; color: ' . $row['role_color'] . '; border: 1px solid ' . $row['role_border'] . '; font-size: 11px; padding: 4px 8px; font-weight: 600;">' . e($row['role_label']) . '</span>';
                })
                ->addColumn('total_activities', function ($row) {
                    if ($row['total_activities'] > 0) {
                        return '<span class="badge badge-pill badge-light-primary font-weight-bold" style="font-size: 12px; padding: 5px 10px;">' . $row['total_activities'] . '</span>';
                    }
                    return '<span class="badge badge-pill badge-light-secondary" style="font-size: 12px; padding: 5px 10px;">0</span>';
                })
                ->addColumn('last_activity', function ($row) {
                    if ($row['total_activities'] === 0) {
                        return '<span class="text-muted font-small-2 font-italic">No action recorded today</span>';
                    }
                    $typeBadge = '<span class="badge badge-light-dark me-50 text-uppercase" style="font-size: 9px; padding: 2px 5px; margin-right: 5px;">' . e($row['last_action_type']) . '</span>';
                    $ipInfo = $row['last_action_ip'] ? '<span class="text-muted font-small-1">[' . e($row['last_action_ip']) . ']</span>' : '';
                    return '
                        <div>
                            ' . $typeBadge . '<span class="font-weight-bold text-dark font-small-3">' . e($row['last_action_desc']) . '</span>
                            <div class="text-muted font-small-2 mt-25">
                                <i data-feather="clock" style="width: 12px; height: 12px; margin-right: 3px;"></i> ' . $row['last_action_time'] . ' (' . $row['last_action_relative'] . ') ' . $ipInfo . '
                            </div>
                        </div>';
                })
                ->addColumn('status', function ($row) {
                    return $row['status_badge'];
                })
                ->addColumn('action', function ($row) use ($selectedDate) {
                    $dayLogUrl = route('admin.user-activities.daily-log', ['role' => $row['role'], 'id' => $row['id'], 'date' => $selectedDate]);
                    return '
                        <div class="d-inline-flex" style="gap: 5px;">
                            <a href="' . $dayLogUrl . '" class="btn btn-sm d-inline-flex align-items-center justify-content-center" style="background-color: #111C38 !important; color: #ffffff !important; border: 1px solid #111C38 !important; border-radius: 6px; font-size: 12px; font-weight: 600; padding: 4px 10px; height: 30px;" title="View Full Day Activity Timeline">
                                <i data-feather="list" style="width: 13px; height: 13px; margin-right: 4px;"></i> Day Log (' . $row['total_activities'] . ')
                            </a>
                        </div>';
                })
                ->rawColumns(['user_info', 'role_badge', 'total_activities', 'last_activity', 'status', 'action'])
                ->make(true);
        }

        // Summary KPI Metrics for the selected date (Staff only)
        $allDayActivities = UserActivity::where('date', $selectedDate)
            ->whereIn('user_role', ['admin', 'worker'])
            ->get();
        $totalActivitiesCount = $allDayActivities->count();

        // Calculate active vs idle vs alert counts across staff users
        $now = Carbon::now();
        $grouped = $allDayActivities->groupBy(function ($item) {
            return $item->user_role . '_' . $item->user_id;
        });

        $activeCount = 0;
        $idleCount = 0;
        $alertCount = 0;

        foreach ($grouped as $acts) {
            $last = $acts->first();
            if ($last) {
                $time = $last->date_time ?: ($last->created_at ?: Carbon::parse($last->date . ' ' . $last->time));
                $diff = $time->diffInMinutes($now);
                if ($diff < 30) {
                    $activeCount++;
                } elseif ($diff < 60) {
                    $idleCount++;
                } else {
                    $alertCount++;
                }
            }
        }

        $totalUsersCount = Admin::count() + Worker::count();

        $rolesList = [
            'all' => 'All Staff / Users',
            'admin' => 'Admin / Staff',
            'worker' => 'Factory Worker',
        ];

        return view('admin.user_activities.index', compact(
            'selectedDate',
            'activeCount',
            'idleCount',
            'alertCount',
            'totalActivitiesCount',
            'totalUsersCount',
            'rolesList'
        ));
    }

    /**
     * Show full day's chronological activity log for a specific staff user with DataTable & Server-Side Pagination.
     * Corresponds to "/company/user-activities/{user}/daily-log" in SAL.
     */
    public function dailyLog(Request $request, string $role, int $id)
    {
        // Resolve staff user entity
        $userObj = null;
        $roleLabel = ucfirst($role);

        if ($role === 'admin') {
            $userObj = Admin::findOrFail($id);
            $roleLabel = 'Admin / Staff';
        } elseif ($role === 'worker') {
            $userObj = Worker::findOrFail($id);
            $roleLabel = 'Factory Worker';
        } else {
            abort(404, 'Only internal staff activity is tracked in this section');
        }

        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        // If AJAX request from DataTable
        if ($request->ajax()) {
            $query = UserActivity::where('date', $selectedDate)
                ->where('user_role', $role)
                ->where('user_id', $id);

            // Filter by action_type
            if ($request->filled('action_type') && $request->action_type !== 'all') {
                $actionType = $request->action_type;
                if ($actionType === 'mutations') {
                    $query->whereIn('event', ['created', 'updated', 'deleted', 'status_change']);
                } elseif ($actionType === 'auth') {
                    $query->whereIn('event', ['login', 'logout']);
                } else {
                    $query->where('action_type', $actionType);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_time', function ($act) {
                    $time = $act->formatted_time;
                    $relative = $act->relative_time;
                    return '
                        <div class="d-flex flex-column">
                            <span class="font-weight-bolder text-dark font-small-3" style="font-family: monospace;">' . e($time) . '</span>
                            <span class="text-muted font-small-1">' . e($relative) . '</span>
                        </div>';
                })
                ->addColumn('action_badge', function ($act) {
                    return $act->action_type_badge;
                })
                ->addColumn('event_description', function ($act) {
                    $route = $act->route_name ? '<span class="text-muted font-small-1 d-block"><i data-feather="navigation" style="width: 10px; height: 10px; margin-right: 3px;"></i> ' . e($act->route_name) . '</span>' : '';
                    return '
                        <div>
                            <span class="font-weight-bold text-dark font-small-3">' . e($act->description ?: $act->eventDetails) . '</span>
                            ' . $route . '
                        </div>';
                })
                ->addColumn('subject_info', function ($act) {
                    if (!$act->subject_type && !$act->subject_ref) {
                        return '<span class="text-muted font-small-2">-</span>';
                    }
                    $modelShort = $act->subject_type ? class_basename($act->subject_type) : '';
                    $ref = $act->subject_ref ?: ($act->subject_id ? '#' . $act->subject_id : '');
                    return '
                        <div>
                            <span class="badge badge-light-secondary font-small-1 font-weight-bold">' . e($modelShort) . '</span>
                            <span class="text-dark font-weight-bold font-small-2 d-block mt-25">' . e($ref) . '</span>
                        </div>';
                })
                ->addColumn('ip_method', function ($act) {
                    $method = strtoupper($act->method ?: 'GET');
                    $methodClass = match ($method) {
                        'POST' => 'badge-light-success',
                        'PUT', 'PATCH' => 'badge-light-warning',
                        'DELETE' => 'badge-light-danger',
                        default => 'badge-light-info',
                    };
                    $ip = $act->ip_address ?: '127.0.0.1';
                    return '
                        <div class="d-flex flex-column align-items-start">
                            <span class="badge ' . $methodClass . ' font-small-1 font-weight-bold text-uppercase">' . $method . '</span>
                            <span class="text-muted font-small-1 mt-25" style="font-family: monospace;">' . e($ip) . '</span>
                        </div>';
                })
                ->addColumn('details_btn', function ($act) {
                    if (empty($act->properties)) {
                        return '<span class="text-muted font-small-2 font-italic">No diff</span>';
                    }
                    $jsonPayload = htmlspecialchars(json_encode($act->properties), ENT_QUOTES, 'UTF-8');
                    return '
                        <button type="button" class="btn btn-sm btn-outline-primary btn-view-diff" data-id="' . $act->id . '" data-event="' . e($act->event) . '" data-subject="' . e($act->subject_ref) . '" data-payload=\'' . $jsonPayload . '\' style="height: 28px; padding: 2px 8px; font-size: 11px; font-weight: 600; border-radius: 4px;">
                            <i data-feather="eye" style="width: 12px; height: 12px; margin-right: 3px;"></i> View Diff
                        </button>';
                })
                ->rawColumns(['formatted_time', 'action_badge', 'event_description', 'subject_info', 'ip_method', 'details_btn'])
                ->orderColumn('formatted_time', 'id $1')
                ->make(true);
        }

        // Metrics breakdown for the day
        $allDayActivities = UserActivity::where('date', $selectedDate)
            ->where('user_role', $role)
            ->where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        $breakdown = [
            'total' => $allDayActivities->count(),
            'orders' => $allDayActivities->where('action_type', 'order')->count(),
            'cart_wishlist' => $allDayActivities->whereIn('action_type', ['cart', 'wishlist'])->count(),
            'mutations' => $allDayActivities->whereIn('event', ['created', 'updated', 'deleted', 'status_change'])->count(),
            'payments' => $allDayActivities->whereIn('action_type', ['payment', 'collection'])->count(),
            'auth' => $allDayActivities->whereIn('event', ['login', 'logout'])->count(),
        ];

        $selectedActionType = $request->input('action_type', 'all');

        return view('admin.user_activities.daily-log', compact(
            'userObj',
            'role',
            'roleLabel',
            'selectedDate',
            'selectedActionType',
            'breakdown'
        ));
    }

    /**
     * Format minutes into readable hour/minute duration string
     */
    protected function formatMinutes(?int $minutes): string
    {
        if ($minutes === null) {
            return '0m';
        }
        if ($minutes < 60) {
            return "{$minutes}m";
        }
        $hours = floor($minutes / 60);
        $rem = $minutes % 60;
        return $rem > 0 ? "{$hours}h {$rem}m" : "{$hours}h";
    }
}
