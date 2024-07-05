<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wordle Game</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .result {
            margin-top: 20px;
        }
        .result-item {
            display: inline-block;
            padding: 5px;
            margin-right: 5px;
            border: 1px solid #ccc;
        }
        .word-display {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .letter-box {
            display: inline-block;
            width: 40px;
            height: 40px;
            margin-right: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 20px;
        }
        .bg-success {
            background-color: #28a745; /* Groen voor correct geraden */
        }
        .bg-warning {
            background-color: #ffc107; /* Geel voor aanwezig maar op verkeerde plaats */
        }
        .bg-danger {
            background-color: #dc3545; /* Rood voor afwezig */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4">Wordle Game</h1>
<!-- Check if user is authenticated -->
@auth

    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
@endauth
        @auth
            <p>Welcome, {{ Auth::user()->name }}!</p>
<!-- Toon eventuele succesberichten -->
            @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif
            <!-- Toon eventuele foutmeldingen -->
@error('message')
    <div class="alert alert-danger" role="alert">
        {{ $message }}
    </div>
@enderror

            <!-- Formulier om te raden -->
            <form method="POST" action="{{ route('guess') }}" id="guess-form">
    @csrf
    <div class="form-group">
        <label for="guess">Enter your guess:</label>
        <input type="text" class="form-control" id="guess" name="guess" maxlength="{{ isset($word) ? strlen($word) : 0 }}">
        <small class="form-text text-muted">The word has {{ isset($word) ? strlen($word) : 0 }} letters.</small>
    </div>
    <button type="submit" class="btn btn-primary">Guess</button>
</form>

            <script>
                document.getElementById('guess-form').addEventListener('submit', function(event) {
                    var guess = document.getElementById('guess').value;
                    if (guess.length !== {{ isset($word) ? strlen($word) : 0 }}) {
                        event.preventDefault();
                        alert('Your guess must be exactly {{ isset($word) ? strlen($word) : 0 }} letters long.');
                    }
                });
            </script>

           <!-- Toon resultaten van de gok -->
@isset($result)
    @if ($result === 'correct')
        <div class="word-display">
            <h2>Guess: {{ $guess }}</h2>
            @foreach (str_split($guess) as $index => $letter)
                <div class="letter-box bg-success">{{ $letter }}</div>
            @endforeach
        </div>
        <div class="alert alert-success" role="alert">
            Congratulations! You guessed the word correctly.
        </div>
    @else
        <div class="word-display">
            <h2>Guess: {{ $guess }}</h2>
            @foreach (str_split($guess) as $index => $letter)
    @php
        $colorClass = '';
        if (isset($result[$index])) {
            if ($result[$index] === 'correct') {
                $colorClass = 'bg-success';
            } elseif ($result[$index] === 'present') {
                $colorClass = 'bg-warning';
            } else {
                $colorClass = 'bg-danger';
            }
        } else {
            $colorClass = 'bg-danger'; // Stel standaardkleur in als geen resultaat beschikbaar is voor de letter
        }
    @endphp
    <div class="letter-box {{ $colorClass }}">{{ $letter }}</div>
@endforeach
        </div>
    @endif
@endisset

            <!-- Toon eerdere gokken -->
            <h2 class="mt-5">Your Past Guesses</h2>
            <ul class="list-group">
                @foreach ($pastGuesses as $pastGuess)
                    <li class="list-group-item">
                        @foreach (str_split($pastGuess->guess) as $index => $letter)
                            @php
                                $colorClass = '';
                                if (isset($pastGuess->result[$index])) {
                                    if ($pastGuess->result[$index] === 'correct') {
                                        $colorClass = 'bg-success';
                                    } elseif ($pastGuess->result[$index] === 'present') {
                                        $colorClass = 'bg-warning';
                                    } else {
                                        $colorClass = 'bg-danger';
                                    }
                                }
                            @endphp
                            <div class="letter-box {{ $colorClass }}">{{ $letter }}</div>
                        @endforeach
                    </li>
                @endforeach
            </ul>

            <!-- Toon scorebord -->
            <h2 class="mt-5">Scoreboard</h2>
            <ul class="list-group">
                @foreach ($scores as $score)
                    <li class="list-group-item">
                        @if ($score->user)
                            {{ $score->user->name }}: {{ $score->correct_guesses }} correct guesses
                        @else
                            User not found
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <!-- Toon inlog- en registratielinks -->
            <p>Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to play.</p>
        @endauth
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
