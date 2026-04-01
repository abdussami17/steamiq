

@extends('layouts.app')
@section('title', 'Dashboard - SteamIQ')


@section('content')
<div class="container">
    <!-- Dashboard Stats -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="bar-chart-3"></i>
                </span>
                Dashboard Overview
            </h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-label">Active Events</div>
                <div class="stat-value">{{ $activeEventsCount ?: 'N/A' }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total Players</div>
                <div class="stat-value">{{ $studentsCount ?: 'N/A' }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Teams Registered</div>
                <div class="stat-value">{{ $teamsCount ?: 'N/A' }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total Organization</div>
                <div class="stat-value">{{ $orgCount ?: 'N/A' }}</div>
            </div>
            {{-- <div class="stat">
                <div class="stat-label">Matches Today</div>
                <div class="stat-value">{{ $todayMatchesCount ?: 'N/A' }}</div>
            </div> --}}
        </div>
    </section>

    <!-- CAM Pillars -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="target"></i>
                </span>
                CAM Performance Pillars
            </h2>
        </div>
        
        <div class="cam-pillars">
            <div class="cam-card">
                <div class="cam-icon">
                    <i data-lucide="brain"></i>
                </div>
                <h3 class="cam-title">Brain Games</h3>
                <p class="cam-description">STEAM challenges across Science, Technology, Engineering, Art, and Math</p>
            </div>
            <div class="cam-card">
                <div class="cam-icon">
                    <i data-lucide="swords"></i>
                </div>
                <h3 class="cam-title">Playground Games</h3>
                <p class="cam-description">Physical sports and outdoor activities</p>
            </div>
            <div class="cam-card">
                <div class="cam-icon">
                    <i data-lucide="gamepad-2"></i>
                </div>
                <h3 class="cam-title">E-Gaming & Esports</h3>
                <p class="cam-description">Digital competitions and esports tournaments</p>
            </div>
        </div>
    </section>

<!-- Recent Activity -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">
            <span class="icon">
                <i data-lucide="activity"></i>
            </span>
            Recent Activity
        </h2>
    </div>

    <div class="card">
        <div class="notifications">
            @forelse($recentActivities as $activity)
                <div class="notification-item">
                    <div class="notification-content">
                        <h6 class="notification-title">{{ $activity->type ?? 'N/A' }}</h6>
                        <p class="notification-text">{{ $activity->description ?? 'N/A' }}</p>
                    </div>
                    <p class="notification-time">
                        {{ $activity->created_at ? $activity->created_at->diffForHumans() : 'N/A' }}
                    </p>
                </div>
            @empty
                <div class="notification-item">
                    <div class="notification-content">
                        <h6 class="notification-title">N/A</h6>
                        <p class="notification-text">No recent activity</p>
                    </div>
                    <p class="notification-time">-</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
   {{-- ══════════════════════════════════════════════════════
         LEADERBOARD SECTION
    ══════════════════════════════════════════════════════ --}}
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="award"></i></span>
                Event Leaderboard
            </h2>
        </div>
    
        <div id="lb-wrapper">
    
            {{-- Controls --}}
            <div id="lb-controls">
                <label for="selectEvent">Event</label>
                <select id="selectEvent">
                    <option value="" hidden>-- Select Event --</option>
                </select>
    
                {{-- Legend --}}
                <div class="lb-legend">
                    <span class="lb-legend-dot"><span style="background:var(--cat-science-bg)"></span>Science</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-tech-bg)"></span>Technology</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-eng-bg)"></span>Engineering</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-art-bg)"></span>Art</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-math-bg)"></span>Math</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-playground-bg)"></span>Playground</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-egaming-bg)"></span>E-Gaming</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-esports-bg)"></span>ESports</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-mission-bg)"></span>Missions</span>

                
                </div>
    
            </div>
    
            {{-- Table --}}
            <div id="lb-scroll">
                <table id="lb-table">
                    <thead id="lb-thead"></thead>
                    <tbody id="lb-tbody"></tbody>
                </table>
            </div>
    
        </div>
    </section>
    
@include('leaderboard.leaderboard-script')
    
   

    <!-- Quick Actions -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="zap"></i>
                </span>
                Quick Actions
            </h2>
        </div>
        
        <div class="card-grid">
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; display: flex; justify-content: center;">
                        <i data-lucide="calendar" style="width: 60px; height: 60px; color: var(--primary);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem;color:var(--text);font-weight:700">Create Event</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Start a new tournament or season</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary" style="width: 100%;">Create Event</a>
                </div>
            </div>
        
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; display: flex; justify-content: center;">
                        <i data-lucide="users" style="width: 60px; height: 60px; color: var(--primary);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem; color:var(--text);font-weight:700">Add Players</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Register new participants</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary" style="width: 100%;">Add Players</a>

                </div>
            </div>
        
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; display: flex; justify-content: center;">
                        <i data-lucide="trophy" style="width: 60px; height: 60px; color: var(--primary);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem; color:var(--text);font-weight:700">Assign Scores</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Assign Player/Team Score</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary" style="width: 100%;">Assign Scores</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

 