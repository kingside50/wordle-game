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
        return redirect()->back()->withErrors(['message' => 'You have reached the maximum number of guesses.']);
    }

    // Retrieve the current word to guess
    $wordToGuess = session('wordle_word');

    $result = $this->checkGuess($guess, $wordToGuess);

    // Save the guess and result
    $guessRecord = new Guess([
        'user_id' => $user->id,
        'guess' => $guess,
        'result' => $result,
    ]);
    $guessRecord->save();

    // Check if all letters are correct to increment the score and clear guesses
    if ($result === 'correct') {
        $this->chooseRandomWord();
        $score = Score::firstOrCreate(['user_id' => $user->id]);
        $score->increment('correct_guesses');
       
        // Clear past guesses
        $user->guesses()->delete();

        // Redirect with success message
        return redirect()->route('wordle.index')->with('success', 'Congratulations! You guessed the word correctly.');
    }

    // Get the user's past guesses
    $pastGuesses = $user->guesses()->latest()->get(); // Verwijder de limiet

    // Retrieve the friends and scores
    $friends = $user->friends()->with('friend')->get();
    $scores = Score::with('user')->orderBy('correct_guesses', 'desc')->take(10)->get();

    return view('wordle', [
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
            return 'wrong_length';
        }

        $wordArray = str_split($word);
        $guessArray = str_split($guess);
        $correctCount = 0;

        for ($i = 0; $i < count($guessArray); $i++) {
            if ($guessArray[$i] === $wordArray[$i]) {
                $correctCount++;
            }
        }

        if ($correctCount === strlen($word)) {
            return 'correct';
        } else {
            return 'incorrect';
        }
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

