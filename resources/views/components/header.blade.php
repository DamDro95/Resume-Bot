<header>

    <img src="images/logo.avif"/>

    <nav>
        <menu>
            @auth
                <li>
                    Hello, {{ Auth::user()->name }}
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button id="logout" type="submit">Logout</button>
                    </form>
                </li>
            @endauth
        </menu>
    </nav>
</header>
