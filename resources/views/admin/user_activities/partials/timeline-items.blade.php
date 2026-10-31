@foreach($activities as $act)
    @php
        $badge = $act->getActionTypeBadge();
        $eventBadge = $act->getEventBadge();
        $props = $act->properties ?? [];
        $hasDiff = !empty($props['old']) && !empty($props['new']);
        $hasAttributes = !empty($props['attributes']);
    @endphp
    <div class="timeline-item position-relative mb-2">
        <!-- Circular Icon Badge -->
        <div class="timeline-icon-badge" style="background-color: #111C38; border: 2px solid #ffffff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: absolute; left: -17px; top: 0; z-index: 2; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
            <i data-feather="{{ $badge['icon'] }}" style="width: 15px; height: 15px; stroke: #ffffff;"></i>
        </div>

        <!-- Activity Card -->
        <div class="card shadow-none mb-0 ms-3" style="margin-left: 28px; background-color: #ffffff; border: 1px solid #efeae2; border-radius: 10px;">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-1">
                    <div>
                        <span class="badge me-1" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 1px solid {{ $badge['border'] }}; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">
                            {{ $badge['label'] }}
                        </span>
                        <span class="badge" style="background-color: {{ $eventBadge['bg'] }}; color: {{ $eventBadge['color'] }}; border: 1px solid {{ $eventBadge['border'] }}; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                            {{ $eventBadge['label'] }}
                        </span>
                        <h6 class="font-weight-bolder text-dark mb-0 mt-50" style="font-size: 13.5px; line-height: 1.4;">
                            {{ $act->description }}
                        </h6>
                    </div>
                    <div class="text-end text-md-right mt-50 mt-md-0">
                        <span class="font-weight-bolder text-dark" style="font-size: 13px;">{{ $act->formatted_time }}</span>
                        <small class="text-muted d-block" style="font-size: 11px;">{{ $act->relative_time }}</small>
                    </div>
                </div>

                <!-- Network Context -->
                <div class="d-flex flex-wrap align-items-center text-muted font-small-2 mb-1" style="gap: 12px; background: #fafaf9; padding: 6px 10px; border-radius: 6px; border: 1px solid #f5f5f4;">
                    @if($act->ip_address)
                        <div><i data-feather="globe" style="width: 12px; height: 12px; margin-right: 3px;"></i> IP: <strong class="text-dark">{{ $act->ip_address }}</strong></div>
                    @endif
                    @if($act->method)
                        <div><i data-feather="terminal" style="width: 12px; height: 12px; margin-right: 3px;"></i> Method: <span class="badge badge-light-secondary font-weight-bold" style="font-size: 9.5px;">{{ $act->method }}</span></div>
                    @endif
                    @if($act->subject_ref)
                        <div><i data-feather="tag" style="width: 12px; height: 12px; margin-right: 3px;"></i> Ref: <strong class="text-dark">{{ $act->subject_ref }}</strong></div>
                    @endif
                    @if($act->url)
                        <div class="text-truncate" style="max-width: 320px;" title="{{ $act->url }}">
                            <i data-feather="link" style="width: 12px; height: 12px; margin-right: 3px;"></i> <small class="text-muted">{{ $act->url }}</small>
                        </div>
                    @endif
                </div>

                <!-- Database Mutation Diff / Attributes Snapshot -->
                @if($hasDiff)
                    <div class="mt-1">
                        <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center py-25 px-50 toggle-diff-btn" type="button" data-bs-toggle="collapse" data-toggle="collapse" data-target="#diff-{{ $act->id }}" aria-expanded="false" style="font-size: 11px; font-weight: 600; border-radius: 4px;">
                            <i data-feather="git-commit" style="width: 12px; height: 12px; margin-right: 4px;"></i> View State Changes (Diff)
                        </button>
                        <div class="collapse mt-1" id="diff-{{ $act->id }}">
                            <div class="table-responsive" style="border-radius: 6px; border: 1px solid #efeae2; overflow: hidden;">
                                <table class="table table-sm table-bordered mb-0" style="background-color: #ffffff; font-size: 12px;">
                                    <thead style="background-color: #f8fafc; font-size: 11px; text-transform: uppercase;">
                                        <tr>
                                            <th style="width: 26%;">Field</th>
                                            <th style="width: 37%; background-color: #fef2f2; color: #991b1b;">Before (Old)</th>
                                            <th style="width: 37%; background-color: #f0fdf4; color: #166534;">After (New)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($props['new'] as $key => $newVal)
                                            @php
                                                $oldVal = isset($props['old'][$key]) ? (is_array($props['old'][$key]) ? json_encode($props['old'][$key]) : $props['old'][$key]) : 'null';
                                                $newValFormatted = is_array($newVal) ? json_encode($newVal) : $newVal;
                                            @endphp
                                            <tr>
                                                <td class="font-weight-bold font-monospace text-dark">{{ $key }}</td>
                                                <td style="background-color: #fffaf0; color: #c2410c; word-break: break-all;">{{ $oldVal }}</td>
                                                <td style="background-color: #f0fdf4; color: #15803d; font-weight: 600; word-break: break-all;">{{ $newValFormatted }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($hasAttributes)
                    <div class="mt-1">
                        <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center py-25 px-50 toggle-diff-btn" type="button" data-bs-toggle="collapse" data-toggle="collapse" data-target="#attr-{{ $act->id }}" aria-expanded="false" style="font-size: 11px; font-weight: 600; border-radius: 4px;">
                            <i data-feather="database" style="width: 12px; height: 12px; margin-right: 4px;"></i> View Recorded Attributes
                        </button>
                        <div class="collapse mt-1" id="attr-{{ $act->id }}">
                            <div class="table-responsive" style="border-radius: 6px; border: 1px solid #efeae2; overflow: hidden;">
                                <table class="table table-sm table-bordered mb-0" style="background-color: #ffffff; font-size: 11.5px;">
                                    <thead style="background-color: #f8fafc; font-size: 10.5px; text-transform: uppercase;">
                                        <tr>
                                            <th style="width: 30%;">Attribute</th>
                                            <th style="width: 70%;">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($props['attributes'] as $attrKey => $attrVal)
                                            <tr>
                                                <td class="font-weight-bold font-monospace text-dark">{{ $attrKey }}</td>
                                                <td style="word-break: break-all;">{{ is_array($attrVal) ? json_encode($attrVal) : ($attrVal ?? 'null') }}</td>
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
