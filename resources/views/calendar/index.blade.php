@extends('layouts.app')
@section('title', 'Calendar')
@section('breadcrumb', 'Calendar')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<style>
.fc { font-family: system-ui, -apple-system, sans-serif; font-size: 0.845rem; }
.fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: #c8cce0; }
.fc .fc-button { background: #1a1e28; border: 1px solid #252936; color: #c8cce0; font-size: 0.8rem; padding: 5px 12px; border-radius: 6px; }
.fc .fc-button:hover { background: #252936; border-color: #3a4060; }
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active { background: rgba(108,99,255,0.2); border-color: #6c63ff; color: #6c63ff; }
.fc .fc-col-header-cell { background: #13161d; color: #6b7590; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; padding: 8px 0; border-color: #252936; }
.fc .fc-daygrid-day { background: #0d0f14; border-color: #1a1e28; }
.fc .fc-daygrid-day:hover { background: #13161d; }
.fc .fc-daygrid-day.fc-day-today { background: rgba(108,99,255,0.06); }
.fc .fc-daygrid-day-number { color: #6b7590; font-size: 0.8rem; padding: 6px 8px; }
.fc .fc-day-today .fc-daygrid-day-number { color: #6c63ff; font-weight: 700; }
.fc .fc-event { border-radius: 4px; font-size: 0.75rem; padding: 1px 5px; border: none; cursor: pointer; }
.fc .fc-event-title { font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.fc .fc-toolbar { margin-bottom: 16px; gap: 8px; }
.fc .fc-scrollgrid { border-color: #252936; }
.fc .fc-scrollgrid td, .fc .fc-scrollgrid th { border-color: #1a1e28; }
.fc .fc-daygrid-more-link { color: #6c63ff; font-size: 0.72rem; }
.fc .fc-popover { background: #1a1e28; border: 1px solid #252936; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
.fc .fc-popover-header { background: #13161d; color: #c8cce0; border-radius: 8px 8px 0 0; font-size: 0.8rem; }
.legend { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 16px; font-size: 0.78rem; }
.legend-item { display: flex; align-items: center; gap: 6px; color: #6b7590; }
.legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Calendar</h2>
        <p>Task deadlines &amp; project milestones</p>
    </div>
</div>

<div class="legend">
    <div class="legend-item"><div class="legend-dot" style="background:#6c63ff;"></div> Project Deadline</div>
    <div class="legend-item"><div class="legend-dot" style="background:rgba(108,99,255,0.3);border:1px solid #6c63ff;"></div> Project Start</div>
    <div class="legend-item"><div class="legend-dot" style="background:#f87171;"></div> High Priority Task</div>
    <div class="legend-item"><div class="legend-dot" style="background:#fbbf24;"></div> Medium Priority Task</div>
    <div class="legend-item"><div class="legend-dot" style="background:#60a5fa;"></div> Low Priority Task</div>
    <div class="legend-item"><div class="legend-dot" style="background:rgba(74,222,128,0.3);border:1px solid #4ade80;"></div> Done Task</div>
</div>

<div class="card" style="padding:20px;">
    <div id="calendar"></div>
</div>

{{-- Event Detail Modal --}}
<div id="eventModal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;">
    <div style="background:#1a1e28;border:1px solid #252936;border-radius:12px;padding:24px;min-width:320px;max-width:440px;width:90%;">
        <div class="row between" style="margin-bottom:14px;">
            <div id="modalTitle" style="font-weight:700;font-size:1rem;color:#c8cce0;"></div>
            <button onclick="closeModal()" style="background:none;border:none;color:#6b7590;cursor:pointer;font-size:1.2rem;line-height:1;">✕</button>
        </div>
        <div id="modalBody" style="font-size:0.845rem;color:#6b7590;line-height:1.7;"></div>
        <div id="modalLink" style="margin-top:16px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,dayGridWeek,listMonth'
        },
        buttonText: {
            today:     'Today',
            month:     'Month',
            week:      'Week',
            list:      'List',
        },
        height: 'auto',
        dayMaxEvents: 4,
        eventSources: [{
            url: '{{ route("calendar.events") }}',
            failure: function() {
                console.warn('Could not load calendar events.');
            }
        }],
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const e = info.event;
            const props = e.extendedProps;

            document.getElementById('modalTitle').textContent = e.title;

            let body = '';
            if (props.status)   body += `<div><span style="color:#4a5068;">Status:</span> <span style="text-transform:capitalize;">${props.status.replace('_',' ')}</span></div>`;
            if (props.priority) body += `<div><span style="color:#4a5068;">Priority:</span> ${props.priority}</div>`;
            if (props.assignee) body += `<div><span style="color:#4a5068;">Assignee:</span> ${props.assignee}</div>`;
            if (e.start)        body += `<div><span style="color:#4a5068;">Date:</span> ${e.start.toLocaleDateString('en-US',{month:'long',day:'numeric',year:'numeric'})}</div>`;

            document.getElementById('modalBody').innerHTML = body;
            document.getElementById('modalLink').innerHTML = e.url
                ? `<a href="${e.url}" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">Open →</a>`
                : '';

            const modal = document.getElementById('eventModal');
            modal.style.display = 'flex';
        },
        eventDidMount: function(info) {
            info.el.style.cursor = 'pointer';
        },
    });

    calendar.render();

    window.closeModal = function() {
        document.getElementById('eventModal').style.display = 'none';
    };
    document.getElementById('eventModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
