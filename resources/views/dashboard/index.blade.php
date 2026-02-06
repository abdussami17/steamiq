

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
                <div class="stat-value">12</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total Players</div>
                <div class="stat-value">284</div>
            </div>
            <div class="stat">
                <div class="stat-label">Teams Registered</div>
                <div class="stat-value">96</div>
            </div>
            <div class="stat">
                <div class="stat-label">Matches Today</div>
                <div class="stat-value">18</div>
            </div>
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
                    <i data-lucide="dribbble"></i>
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
            <div style="display: grid; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--dark); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600;">Match #1 Completed</div>
                        <div style="color: var(--text-dim); font-size: 0.85rem;">Team Alpha defeated Team Echo 2-0</div>
                    </div>
                    <div style="color: var(--text-dim); font-size: 0.85rem;">5 min ago</div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--dark); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600;">New Player Registered</div>
                        <div style="color: var(--text-dim); font-size: 0.85rem;">Sarah Williams joined Team Gamma</div>
                    </div>
                    <div style="color: var(--text-dim); font-size: 0.85rem;">12 min ago</div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--dark); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600;">Score Updated</div>
                        <div style="color: var(--text-dim); font-size: 0.85rem;">Brain Games scores processed for Event #1</div>
                    </div>
                    <div style="color: var(--text-dim); font-size: 0.85rem;">28 min ago</div>
                </div>
            </div>
        </div>
    </section>

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
                    <h3 style="margin-bottom: 0.5rem;">Create Event</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Start a new tournament or season</p>
                    <button class="btn btn-primary" style="width: 100%;" onclick="openModal('createEventModal')">Create Event</button>
                </div>
            </div>
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; display: flex; justify-content: center;">
                        <i data-lucide="users" style="width: 60px; height: 60px; color: var(--primary);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">Add Players</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Register new participants</p>
                    <button class="btn btn-primary" style="width: 100%;" onclick="openPlayerModal('add')">Add Player</button>
                </div>
            </div>
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; display: flex; justify-content: center;">
                        <i data-lucide="trophy" style="width: 60px; height: 60px; color: var(--primary);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">Generate Bracket</h3>
                    <p style="color: var(--text-dim); margin-bottom: 1.5rem;">Create tournament brackets</p>
                    <a href="tournaments.html" class="btn btn-primary" style="width: 100%;">View Tournaments</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection