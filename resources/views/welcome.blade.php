<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Wordle!</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Welcome to Wordle!</div>

                    <div class="card-body">
                        <p>Welcome to Wordle, a fun word-guessing game!</p>
                        <p>Please login or register to start playing and challenging your friends.</p>

                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
