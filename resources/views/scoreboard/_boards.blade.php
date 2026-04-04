{{--
    resources/views/scoreboard/_boards.blade.php
    Server-side initial render of both Primary + Junior boards.
    Props: $selectedEvent, $primaryData, $juniorData, $activities
--}}

@if(!$selectedEvent)
    <div class="pg-empty">No events found. Please create an event first.</div>
@else

    {{-- PRIMARY --}}
    @if(!empty($primaryData))
        @include('scoreboard._single_board', [
            'eventName'  => $selectedEvent->name,
            'division'   => 'Primary',
            'rows'       => $primaryData,
            'activities' => $activities,
        ])
    @endif

    {{-- Gap --}}
    @if(!empty($primaryData) && !empty($juniorData))
        <div class="pg-gap"></div>
    @endif

    {{-- JUNIOR --}}
    @if(!empty($juniorData))
        @include('scoreboard._single_board', [
            'eventName'  => $selectedEvent->name,
            'division'   => 'Junior',
            'rows'       => $juniorData,
            'activities' => $activities,
        ])
    @endif

    {{-- Nothing at all --}}
    @if(empty($primaryData) && empty($juniorData))
        <div class="pg-empty">No scoreboard data found for this event.</div>
    @endif

@endif