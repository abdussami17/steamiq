@extends('layouts.app')
@section('title', 'Settings - SteamIQ')


@section('content')
    <div class="container">


        <!-- Settings -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="settings"></i>
                    </span>
                    Settings
                </h2>
            </div>



            <button class="tab active" onclick="switchTab('users', event)">Users</button>
            <button class="tab" onclick="switchTab('activities', event)">Activities</button>
            <button class="tab" onclick="switchTab('cards', event)">Cards</button>
            <button class="tab" onclick="switchTab('profile', event)">Profile</button>



            <div id="users-tab" class="tab-content show active">
                <div class="spreadsheet-container mt-4">

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $us)
                                <tr>
                                    <td>{{ $us->id ?? 'N/A' }}</td>
                                    <td>{{ $us->name ?? 'N/A' }}</td>
                                    <td>{{ $us->username ?? 'N/A' }}</td>
                                    <td>{{ $us->email ?? 'N/A' }}</td>
                                    <td>
                                        {{ isset($us->role) ? ($us->role == 1 ? 'Admin' : 'User') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $us->created_at ? \Carbon\Carbon::parse($us->created_at)->format('M d, Y') : 'N/A' }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users currently</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>



                </div>
            </div>

            <div class="tab-content" id="activities-tab">
                <div class="spreadsheet-container mt-4">

                    <!-- Filters -->
                    <div class="activities-filter-container d-flex align-items-center gap-2 mb-3">
                        <input type="text" id="activities-filter-search-input" class="w-25 form-input"
                            placeholder="Search activities...">

                        <select id="activities-filter-date-range" class=" form-select w-25">
                            <option value="all">All Dates</option>
                            <option value="24h">Last 24 Hours</option>
                            <option value="3d">Last 3 Days</option>
                            <option value="30d">Last 30 Days</option>
                            <option value="6m">Last 6 Months</option>
                        </select>
                    </div>

                    <table id="activities-data-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Type</th>
                                <th>Activity Type</th>
                                <th>Activity Name</th>
                                <th>Max Score</th>
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody id="activities-data-table-body">
                            <tr>
                                <td colspan="7" class="text-center">Loading activities...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="cards-tab" class="tab-content">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-target="#addCardModal" data-bs-toggle="modal"><i
                                data-lucide="plus"></i>Add Card</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cards as $card)
                                    <tr>
                                        <td>{{ $card->id }}</td>
                                        <td class="text-capitalize">{{ $card->type }}</td>
                                        <td>
                                            @if ($card->negative_points == 0)
                                                Deducted All Points
                                            @else
                                                {{ $card->negative_points }}
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:0.25rem;">
                                                <button class="btn btn-icon btn-edit editCardBtn"
                                                    data-id="{{ $card->id }}" data-type="{{ $card->type }}"
                                                    data-points="{{ $card->negative_points }}" data-bs-toggle="modal"
                                                    data-bs-target="#editCardModal">
                                                    <i data-lucide="edit-2"></i>
                                                </button>

                                                <form action="{{ route('cards.delete', $card->id) }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @push('modals')
                        @include('card.create')
                        @include('card.edit')
                    @endpush

                </div>
            </div>
            <div id="profile-tab" class="tab-content">
                <div class="spreadsheet-container mt-4">
              
                  <div class="sems-profile-wrapper">
                    <div class="sems-profile-card">
              
                      {{-- Hero Banner --}}
                      <div class="sems-profile-hero-banner">
                        <div class="d-flex align-items-center gap-3 mb-3">
                          <div class="sems-profile-avatar-ring" id="semsProfileAvatarInitials">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                          </div>
                          <div>
                            <p class="sems-profile-hero-name" id="semsProfileHeroName">{{ auth()->user()->name }}</p>
                          
                          </div>
                          <div class="ms-auto">
                            <div class="sems-profile-badge-pill">
                              <span class="sems-profile-badge-dot"></span>
                              Active
                            </div>
                          </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                         
                          <div class="sems-profile-stat-tile">
                            <p class="sems-profile-stat-label">Member Since</p>
                            <p class="sems-profile-stat-value">{{ auth()->user()->created_at->year }}</p>
                          </div>
                          <div class="sems-profile-stat-tile">
                            <p class="sems-profile-stat-label">Role</p>
                            <p class="sems-profile-stat-value">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</p>
                          </div>
                        </div>
                      </div>
              
                      <form id="profile-update-form">
                        @csrf
              
                        {{-- Account Details --}}
                        <div class="sems-profile-body-section">
                          <div class="sems-profile-section-header">
                            <div class="sems-profile-section-icon">
                              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 14c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            </div>
                            <span class="sems-profile-section-title">Account details</span>
                          </div>
              
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="sems-profile-label" for="semsNameInput">Full name</label>
                              <input class="sems-profile-input form-control" id="semsNameInput" name="name"
                                type="text" value="{{ auth()->user()->name }}" placeholder="Your full name">
                            </div>
                            <div class="col-md-6">
                              <label class="sems-profile-label" for="semsUsernameInput">Username</label>
                              <div class="sems-profile-input-wrap">
                                <input class="sems-profile-input form-control" id="semsUsernameInput" name="username"
                                  type="text" value="{{ auth()->user()->username }}" placeholder="username"
                                  style="padding-left:28px;">
                                <span class="sems-profile-at-prefix">@</span>
                              </div>
                            </div>
                          </div>
                        </div>
              
                        <hr class="sems-profile-divider">
              
                        {{-- Password Section --}}
                        <div class="sems-profile-body-section">
                          <div class="sems-profile-section-header">
                            <div class="sems-profile-section-icon">
                              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><rect x="2" y="7" width="12" height="8" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            </div>
                            <span class="sems-profile-section-title">Change password</span>
                            <span class="sems-profile-optional-tag ms-auto">Optional</span>
                          </div>
              
                          <div class="mb-3">
                            <label class="sems-profile-label">Current password</label>
                            <div class="sems-profile-input-wrap">
                              <input class="sems-profile-input form-control" id="profile-current-password"
                                name="current_password" type="password" placeholder="Enter current password">
                              <button type="button" class="sems-profile-eye-btn toggle-pass"
                                data-target="profile-current-password" data-sems-target="profile-current-password">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><ellipse cx="8" cy="8" rx="6" ry="4" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="1.8" fill="currentColor"/></svg>
                              </button>
                            </div>
                          </div>
              
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="sems-profile-label">New password</label>
                              <div class="sems-profile-input-wrap">
                                <input class="sems-profile-input form-control" id="profile-password-input"
                                  name="password" type="password" placeholder="Min. 6 characters">
                                <button type="button" class="sems-profile-eye-btn toggle-pass"
                                  data-target="profile-password-input" data-sems-target="profile-password-input">
                                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><ellipse cx="8" cy="8" rx="6" ry="4" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="1.8" fill="currentColor"/></svg>
                                </button>
                              </div>
                              <div class="sems-profile-strength-track"><div class="sems-profile-strength-fill" id="semsStrengthFill"></div></div>
                              <p class="sems-profile-strength-label" id="semsStrengthLabel"></p>
                            </div>
                            <div class="col-md-6">
                              <label class="sems-profile-label">Confirm password</label>
                              <div class="sems-profile-input-wrap">
                                <input class="sems-profile-input form-control" id="profile-password-confirm-input"
                                  name="password_confirmation" type="password" placeholder="Repeat new password">
                                <button type="button" class="sems-profile-eye-btn toggle-pass"
                                  data-target="profile-password-confirm-input" data-sems-target="profile-password-confirm-input">
                                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><ellipse cx="8" cy="8" rx="6" ry="4" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="1.8" fill="currentColor"/></svg>
                                </button>
                              </div>
                              <p class="sems-profile-strength-label" id="semsConfirmMatchLabel"></p>
                            </div>
                          </div>
                        </div>
              
                        <div id="profile-update-message" class="px-4"></div>
              
                        <div class="sems-profile-actions-row">
                          <button type="button" class="sems-profile-cancel-btn" id="semsResetBtn">Reset</button>
                          <button type="submit" class="sems-profile-submit-btn">Save changes</button>
                        </div>
              
                      </form>
                    </div>
                  </div>
              
                </div>
              </div>
         @push('scripts')
             @include('settings.scripts.profile-script')
             @include('settings.scripts.activity-script')
         @endpush






        </section>


    </div>
@endsection
