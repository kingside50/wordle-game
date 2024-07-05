<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Score;
use App\Models\Guess;
use App\Models\Friend;

class WordleController extends Controller
{
    protected $words = ['apple', 'banana', 'cherry', 'date', 'elderberry'];

    public function index()
    {
        $user = Auth::user();

        // Haal de vrienden van de huidige gebruiker op
        $friends = $user->friends()->with('friend')->get();

        // Kies een willekeurig woord als het niet is ingesteld in de sessie
        if (!session()->has('wordle_word')) {
            $this->chooseRandomWord();
        }

        $scores = Score::orderBy('correct_guesses', 'desc')->take(10)->get();
        $pastGuesses = $user->guesses()->latest()->take(10)->get();

        return view('wordle', [
            'scores' => $scores,
            'user' => $user,
            'pastGuesses' => $pastGuesses,
            'word' => session('wordle_word'),
            'friends' => $friends, // Voeg vrienden toe aan de view
        ]);
    }

    protected function chooseRandomWord()
    {
        $randomWord = $this->words[array_rand($this->words)];
        session(['wordle_word' => $randomWord]);
    }

    public function guess(Request $request)
    {
        $user = Auth::user();
        $guess = $request->input('guess');
    
        // Check if user has reached maximum guesses
        if ($user->guesses()->count() >= 10) {
            $user->guesses()->delete();
            return redirect()->route('wordle.index')->withErrors(['message' => 'You have reached the maximum number of guesses.']);
        }
    
        // Retrieve the current word to guess
        $wordToGuess = session('wordle_word');
    
        $guessResult = $this->checkGuess($guess, $wordToGuess);
        $status = $guessResult['status'];
        $result = $guessResult['result'];
    
        // Save the guess and result
        $guessRecord = new Guess([
            'user_id' => $user->id,
            'guess' => $guess,
            'result' => json_encode($result), // Save the result as JSON
        ]);
        $guessRecord->save();
    
        // Check if all letters are correct to increment the score and clear guesses
        if ($status === 'correct') {
            // Increment the user's score
            $score = Score::firstOrCreate(['user_id' => $user->id]);
            $score->increment('correct_guesses');
            
            // Clear past guesses
            $user->guesses()->delete();
    
            // Choose a new random word
            $this->chooseRandomWord();
    
            // Redirect with success message
            return redirect()->route('wordle.index')->with('success', 'Congratulations! You guessed the word correctly.');
        }
    
        // Get the user's past guesses
        $pastGuesses = $user->guesses()->latest()->get();
    
        // Retrieve the friends and scores
        $friends = $user->friends()->with('friend')->get();
        $scores = Score::with('user')->orderBy('correct_guesses', 'desc')->take(10)->get();
    
        return view('wordle', [
            'status' => $status,
            'result' => $result,
            'guess' => $guess,
            'friends' => $friends,
            'scores' => $scores,
            'pastGuesses' => $pastGuesses,
            'word' => session('wordle_word'),
        ]);
    }

protected function checkGuess($guess, $word)
{
    if (strlen($guess) !== strlen($word)) {
        return ['status' => 'wrong_length', 'result' => []];
    }

    $wordArray = str_split($word);
    $guessArray = str_split($guess);
    $result = array_fill(0, strlen($word), 'absent');
    $correctCount = 0;

    // Stap 1: Markeer correcte letters
    foreach ($guessArray as $i => $letter) {
        if ($letter === $wordArray[$i]) {
            $result[$i] = 'correct';
            $wordArray[$i] = null; // Verwijder de gemarkeerde correcte letter
            $correctCount++;
        }
    }

    // Stap 2: Markeer letters die aanwezig zijn op een verkeerde plek
    foreach ($guessArray as $i => $letter) {
        if ($result[$i] !== 'correct' && in_array($letter, $wordArray)) {
            $result[$i] = 'present';
            $wordArray[array_search($letter, $wordArray)] = null; // Verwijder de gemarkeerde aanwezige letter
        }
    }

    return ['status' => $correctCount === strlen($word) ? 'correct' : 'incorrect', 'result' => $result];
}
    public function dashboard()
{
    $user = Auth::user();

    // Retrieve the user's friends
    $friends = $user->friends()->with('friend')->get();

    // Retrieve the leaderboard scores for friends
    $friendScores = Score::whereIn('user_id', $friends->pluck('friend_id'))->orderBy('correct_guesses', 'desc')->get();

    return view('dashboard', [
        'user' => $user,
        'friends' => $friends,
        'friendScores' => $friendScores,
    ]);
}
}

