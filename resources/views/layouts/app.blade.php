<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LocalMarket') | LocalMarket</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg lm-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand lm-brand" href="{{ route('home') }}">
            <i class="fa-solid fa-store"></i> Local<span>Market</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <form class="d-flex flex-grow-1 mx-lg-4 my-3 my-lg-0" action="{{ route('home') }}" method="GET">
                <div class="input-group lm-search">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search for products or services...">
                    <button class="btn btn-lm" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            <ul class="navbar-nav align-items-lg-center gap-lg-2">
                @guest
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-outline-lm" href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-circle-user"></i> {{ Auth::user()->name }}
                        </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('listings.my') }}">
                                    <i class="fa-solid fa-rectangle-list"></i> My Ads
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a class="btn btn-lm" href="{{ url('/listings/create') }}"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    @yield('content')
</main>

<footer class="lm-footer mt-5 py-4">
    <div class="container text-center">
        <div class="lm-brand mb-1"><i class="fa-solid fa-store"></i> Local<span>Market</span></div>
        <small class="text-muted">&copy; {{ date('Y') }} LocalMarket. Buy and sell locally.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>