

@extends('layouts.app')
@section('title', 'Players - SteamIQ')


@section('content')
<div class="container">
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="users"></i>
                </span>
                Players Management
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#playerModal">
                <i data-lucide="plus"></i>
                <span>Add New Player</span>
            </button>
        </div>

        <div class="spreadsheet-container">
            <div class="spreadsheet-toolbar">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#playerModal">
                    <i data-lucide="plus"></i> Add Player
                </button>
                <button class="btn btn-secondary" onclick="importPlayers()">
                    <i data-lucide="download"></i> Import CSV
                </button>
                <button class="btn btn-secondary">
                    <i data-lucide="upload"></i> Export
                </button>
                <button class="btn btn-secondary">
                    <i data-lucide="refresh-cw"></i> Refresh
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Player ID</th>
                        <th>Name</th>
                        <th>Team</th>
                        <th>Brain Points</th>
                        <th>Playground Points</th>
                        <th>E-Gaming Points</th>
                        <th>Total</th>
                        <th>Rank</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" value="P001" readonly></td>
                        <td><input type="text" value="Alex Johnson"></td>
                        <td><input type="text" value="Team Alpha"></td>
                        <td><input type="number" value="450"></td>
                        <td><input type="number" value="380"></td>
                        <td><input type="number" value="520"></td>
                        <td style="color: var(--primary); font-weight: 700;">1350</td>
                        <td style="color: #FFD700; font-weight: 700;">1</td>
                        <td>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-icon btn-edit" onclick="openPlayerModal('edit', 'P001')" title="Edit">
                                    <i data-lucide="edit-2"></i>
                                </button>
                                <button class="btn btn-icon btn-delete" onclick="confirmDelete('player', 'P001', 'Alex Johnson')" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="text" value="P002" readonly></td>
                        <td><input type="text" value="Maria Garcia"></td>
                        <td><input type="text" value="Team Beta"></td>
                        <td><input type="number" value="420"></td>
                        <td><input type="number" value="410"></td>
                        <td><input type="number" value="480"></td>
                        <td style="color: var(--primary); font-weight: 700;">1310</td>
                        <td style="color: #C0C0C0; font-weight: 700;">2</td>
                        <td>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-icon btn-edit" onclick="openPlayerModal('edit', 'P002')" title="Edit">
                                    <i data-lucide="edit-2"></i>
                                </button>
                                <button class="btn btn-icon btn-delete" onclick="confirmDelete('player', 'P002', 'Maria Garcia')" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="text" value="P003" readonly></td>
                        <td><input type="text" value="James Chen"></td>
                        <td><input type="text" value="Team Alpha"></td>
                        <td><input type="number" value="390"></td>
                        <td><input type="number" value="440"></td>
                        <td><input type="number" value="460"></td>
                        <td style="color: var(--primary); font-weight: 700;">1290</td>
                        <td style="color: #CD7F32; font-weight: 700;">3</td>
                        <td>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-icon btn-edit" onclick="openPlayerModal('edit', 'P003')" title="Edit">
                                    <i data-lucide="edit-2"></i>
                                </button>
                                <button class="btn btn-icon btn-delete" onclick="confirmDelete('player', 'P003', 'James Chen')" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="text" value="P004" readonly></td>
                        <td><input type="text" value="Sarah Williams"></td>
                        <td><input type="text" value="Team Gamma"></td>
                        <td><input type="number" value="410"></td>
                        <td><input type="number" value="370"></td>
                        <td><input type="number" value="490"></td>
                        <td style="color: var(--primary); font-weight: 700;">1270</td>
                        <td style="font-weight: 700;">4</td>
                        <td>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-icon btn-edit" onclick="openPlayerModal('edit', 'P004')" title="Edit">
                                    <i data-lucide="edit-2"></i>
                                </button>
                                <button class="btn btn-icon btn-delete" onclick="confirmDelete('player', 'P004', 'Sarah Williams')" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>






@endsection

<!-- Add/Edit Player Modal -->
<div class="modal fade" id="playerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form  action="{{ route('player.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                   

                    <div class="form-group">
                        <label class="form-label">Player Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-input" name="player_name" placeholder="Enter Player Name">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-input"  name="player_email" placeholder="Enter Player Email">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <select class="form-select" name="event_id">
                            <option hidden>-- Select Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Player</button>
                </div>
            </form>

        </div>
    </div>
</div>


    