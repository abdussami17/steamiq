@extends('layouts.app')
@section('title', 'Settings - SteamIQ')


@section('content')
    <div class="container">

        <!-- Leaderboard -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="award"></i>
                    </span>
                    Cards Operation
                </h2>
                <button class="btn btn-primary" data-bs-target="#addCardModal" data-bs-toggle="modal"><i data-lucide="plus"></i>Add Card</button>
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
                                <td>{{ $card->type }}</td>
                                <td>
                                    @if ($card->negative_points == 0)
                                        Deducted All Points
                                    @else
                                        {{ $card->negative_points }}
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:0.25rem;">
                                        <button class="btn btn-icon btn-edit editCardBtn" data-id="{{ $card->id }}"
                                            data-type="{{ $card->type }}" data-points="{{ $card->negative_points }}"
                                            data-bs-toggle="modal" data-bs-target="#editCardModal">
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
        </section>





    </div>


    @include('card.create')
    @include('card.edit')

@endsection
