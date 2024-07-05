<x<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Voeg hier de knop toe -->
                    <a href="{{ route('wordle.index') }}" class="btn btn-primary">Go to Wordle</a>
                    <h2>Welcome, {{ $user->name }}</h2>
                    
                    <!-- Formulier om een vriend toe te voegen -->
                    <form method="POST" action="{{ route('friend.add') }}" class="mt-4">
                        @csrf
                        <div class="form-group">
                            <label for="friend_email">Enter friend's email:</label>
                            <input type="email" class="form-control" id="friend_email" name="friend_email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Friend</button>
                    </form>

                    <!-- Toon eventuele succes- of foutmeldingen voor vriend toevoegen -->
                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Toon lijst met vrienden -->
                    <h3 class="mt-5">Your Friends:</h3>
                    @if ($friends->isEmpty())
                        <p>You have no friends yet.</p>
                    @else
                        <ul>
                            @foreach ($friends as $friend)
                                <li>{{ $friend->friend->name }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <!-- Toon leaderboard voor vrienden -->
                    <h3 class="mt-5">Friend Leaderboard:</h3>
                    @if ($friendScores->isEmpty())
                        <p>No scores found for your friends.</p>
                    @else
                        <ul>
                            @foreach ($friendScores as $score)
                                <li>{{ $score->user->name }}: {{ $score->correct_guesses }} correct guesses</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
