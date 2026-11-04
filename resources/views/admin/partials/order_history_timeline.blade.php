<div class="order-history-wrapper">
    <!-- Header Summary Card -->
    <div class="card bg-light border-0 mb-3 shadow-none">
        <div class="card-body p-2">
            <div class="row align-items-center">
                <div class="col-md-7 col-12">
                    <span class="badge badge-light-secondary text-uppercase mb-50">{{ $typeLabel }}</span>
                    <h4 class="mb-25 font-weight-bolder text-primary">
                        <i data-feather="file-text" style="width: 20px; height: 20px;" class="mr-50"></i>
                        {{ $orderNumber }}
                    </h4>
                    <span class="text-muted small">Complete chronological audit trail and activity log</span>
                </div>
                <div class="col-md-5 col-12 text-md-right mt-1 mt-md-0">
                    <div class="d-inline-block text-left mr-2">
                        <small class="text-muted d-block font-weight-bold">Current Status</small>
                        <span class="badge badge-pill badge-light-primary text-uppercase font-weight-bolder px-1 py-50">
                            {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                        </span>
                    </div>
                    <div class="d-inline-block text-left">
                        <small class="text-muted d-block font-weight-bold">Total Events</small>
                        <span class="badge badge-pill badge-secondary font-weight-bolder px-1 py-50">
                            {{ $activities->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Body -->
    @if($activities->isEmpty())
        <div class="text-center py-4">
            <div class="mb-1">
                <i data-feather="clock" style="width: 48px; height: 48px; stroke: #9e9e9e;"></i>
            </div>
            <h5 class="text-muted">No activity records found for this order.</h5>
            <p class="text-muted small">New lifecycle activities will automatically be recorded here as the order progresses.</p>
        </div>
    @else
        <div class="order-activity-timeline position-relative pl-3" style="border-left: 2px solid #e2e8f0; margin-left: 14px;">
            @foreach($activities as $act)
                @php
                    $actionBadge = $act->action_badge;
                    $actorBadge  = $act->actor_badge;
                    $hasProps    = !empty($act->properties);
                @endphp
                <div class="timeline-event position-relative mb-3">
                    <!-- Icon Pin -->
                    <div class="timeline-pin position-absolute shadow-sm"
                         style="left: -32px; top: 0; width: 34px; height: 34px; border-radius: 50%; background: {{ $actionBadge['bg'] }}; border: 2px solid {{ $actionBadge['border'] }}; display: flex; align-items: center; justify-content: center; z-index: 2;">
                        <i data-feather="{{ $actionBadge['icon'] }}" style="width: 16px; height: 16px; stroke: {{ $actionBadge['color'] }};"></i>
                    </div>

                    <!-- Event Card -->
                    <div class="card border mb-0" style="border-color: #e5e7eb; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="card-body p-2">
                            <!-- Card Header: Badges & Timestamp -->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-1">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                    <!-- Action Badge -->
                                    <span class="badge" style="background-color: {{ $actionBadge['bg'] }}; color: {{ $actionBadge['color'] }}; border: 1px solid {{ $actionBadge['border'] }}; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 4px; text-transform: uppercase;">
                                        {{ $actionBadge['label'] }}
                                    </span>

                                    <!-- Actor Badge -->
                                    <span class="badge" style="background-color: {{ $actorBadge['bg'] }}; color: {{ $actorBadge['color'] }}; font-weight: 600; font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                        <i data-feather="{{ $actorBadge['icon'] }}" style="width: 11px; height: 11px; margin-right: 3px;"></i>
                                        {{ $actorBadge['label'] }}
                                    </span>
                                </div>

                                <div class="text-right mt-50 mt-sm-0">
                                    <div class="font-weight-bold text-dark" style="font-size: 12.5px;">
                                        <i data-feather="calendar" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                                        {{ $act->formatted_time }}
                                    </div>
                                    <small class="text-muted">{{ $act->relative_time }}</small>
                                </div>
                            </div>

                            <!-- Description -->
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13.5px; line-height: 1.5;">
                                {{ $act->description }}
                            </h6>

                            <!-- Performed By User Detail -->
                            <div class="p-1 mb-1 rounded" style="background: #f8fafc; border: 1px solid #edf2f7; font-size: 12px;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div>
                                        <span class="text-muted">Action Performed By:</span>
                                        <strong class="text-dark ml-25">{{ $act->actor_name }}</strong>
                                        @if($act->actor_id)
                                            <span class="badge badge-light-secondary font-weight-normal ml-50">ID #{{ $act->actor_id }}</span>
                                        @endif
                                    </div>
                                    @if($act->ip_address)
                                        <div class="text-muted small">
                                            <i data-feather="globe" style="width: 11px; height: 11px; margin-right: 2px;"></i> IP: {{ $act->ip_address }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status Transition (if applicable) -->
                            @if($act->from_status || $act->to_status)
                                <div class="d-flex align-items-center mb-1 flex-wrap" style="gap: 8px;">
                                    <span class="small font-weight-bold text-muted">Status Transition:</span>
                                    <span class="badge badge-light-secondary" style="font-size: 11px;">
                                        {{ ucfirst(str_replace('_', ' ', $act->from_status ?? 'initial')) }}
                                    </span>
                                    <i data-feather="arrow-right" style="width: 14px; height: 14px; stroke: #64748b;"></i>
                                    <span class="badge badge-light-success font-weight-bolder" style="font-size: 11px;">
                                        {{ ucfirst(str_replace('_', ' ', $act->to_status ?? 'N/A')) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Remarks / Comments (if applicable) -->
                            @if(!empty($act->remarks))
                                <div class="alert alert-secondary py-1 px-1 mb-1" style="background: #fffbeb; border-color: #fde68a; color: #92400e; font-size: 12px;">
                                    <strong><i data-feather="message-square" style="width: 12px; height: 12px; margin-right: 4px;"></i> Remarks / Notes:</strong>
                                    <div class="mt-25">{{ $act->remarks }}</div>
                                </div>
                            @endif

                            <!-- Collapsible Additional Details / Properties -->
                            @if($hasProps)
                                <div class="mt-50">
                                    <button class="btn btn-sm btn-outline-secondary py-25 px-50" type="button" data-toggle="collapse" data-target="#details-{{ $act->id }}" aria-expanded="false" style="font-size: 11px;">
                                        <i data-feather="list" style="width: 12px; height: 12px; margin-right: 3px;"></i> View Event Details
                                    </button>
                                    <div class="collapse mt-1" id="details-{{ $act->id }}">
                                        <div class="table-responsive bg-light p-1 rounded" style="border: 1px solid #e2e8f0;">
                                            <table class="table table-sm table-borderless mb-0" style="font-size: 11.5px;">
                                                <tbody>
                                                    @foreach($act->properties as $propKey => $propVal)
                                                        <tr>
                                                            <th style="width: 30%;" class="text-capitalize text-muted">{{ str_replace('_', ' ', $propKey) }}:</th>
                                                            <td style="width: 70%;">
                                                                @if(is_array($propVal))
                                                                    <pre class="mb-0 bg-white p-50 rounded border" style="font-size: 10.5px; max-height: 120px; overflow-y: auto;">{{ json_encode($propVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                @elseif(is_bool($propVal))
                                                                    <span class="badge badge-light-info">{{ $propVal ? 'true' : 'false' }}</span>
                                                                @else
                                                                    <strong>{{ (string) $propVal }}</strong>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
