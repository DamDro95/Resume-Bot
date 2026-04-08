<header>

    <img src="images/logo.avif"/>

    <nav>
        <menu>
            @auth
                <li>
                    <a wire:click="logout">Logout</a>
                </li>
            @endauth
        </menu>
    </nav>
</header>
