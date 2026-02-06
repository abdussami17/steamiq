

@extends('layouts.app')
@section('title', 'Tournaments - SteamIQ')


@section('content')
<div class="container">
    <!-- Tournament Bracket -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="flag"></i>
                </span>
                Tournament Bracket
            </h2>
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-secondary">Generate Bracket</button>
                <button class="btn btn-primary">Display on TV</button>
            </div>
        </div>

        <div class="bracket-container">
            <div class="bracket">
                <!-- Semifinals -->
                <div class="bracket-round">
                    <div class="bracket-round-title">Semifinals</div>
                    <div class="bracket-match">
                        <div class="bracket-team winner">
                            <span class="bracket-team-name">Team Alpha</span>
                            <span class="bracket-team-score">2</span>
                        </div>
                        <div class="bracket-team">
                            <span class="bracket-team-name">Team Echo</span>
                            <span class="bracket-team-score">0</span>
                        </div>
                    </div>
                    <div class="bracket-match">
                        <div class="bracket-team winner">
                            <span class="bracket-team-name">Team Beta</span>
                            <span class="bracket-team-score">2</span>
                        </div>
                        <div class="bracket-team">
                            <span class="bracket-team-name">Team Delta</span>
                            <span class="bracket-team-score">1</span>
                        </div>
                    </div>
                </div>

                <!-- Finals -->
                <div class="bracket-round">
                    <div class="bracket-round-title">Finals</div>
                    <div class="bracket-match">
                        <div class="bracket-team">
                            <span class="bracket-team-name">Team Alpha</span>
                            <span class="bracket-team-score">-</span>
                        </div>
                        <div class="bracket-team">
                            <span class="bracket-team-name">Team Beta</span>
                            <span class="bracket-team-score">-</span>
                        </div>
                    </div>
                </div>

                <!-- Champion -->
                <div class="bracket-round">
                    <div class="bracket-round-title">Champion</div>
                    <div class="bracket-match" style="background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 212, 255, 0.1)); border-color: var(--primary);">
                        <div class="bracket-team" style="border: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <span class="bracket-team-name">TBD</span>
                            <i data-lucide="trophy" style="width: 24px; height: 24px; color: var(--primary);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tournament Settings -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="settings"></i>
                </span>
                Tournament Settings
            </h2>
        </div>

        <div class="card">
            <form>
                <div style="display: grid; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Tournament Type</label>
                        <select class="form-select">
                            <option value="single">Single Elimination</option>
                            <option value="double">Double Elimination</option>
                            <option value="roundrobin">Round Robin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Seeding Method</label>
                        <select class="form-select">
                            <option value="rank">By Rank (CAM Points)</option>
                            <option value="random">Random</option>
                            <option value="manual">Manual Seeding</option>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Number of Teams</label>
                            <input type="number" class="form-input" value="32" min="4" max="64">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bracket Size</label>
                            <select class="form-select">
                                <option value="8">8 Teams</option>
                                <option value="16">16 Teams</option>
                                <option value="32" selected>32 Teams</option>
                                <option value="64">64 Teams</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Match Format</label>
                        <select class="form-select">
                            <option value="single">Single Round</option>
                            <option value="bo3">Best of 3 (First to 2)</option>
                            <option value="bo5">Best of 5 (First to 3)</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="button" class="btn btn-secondary" style="flex: 1;">Reset</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Generate Bracket</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection