<header>
    <div class="top-nav container">
        <div class="top-nav-left">
            <div class="logo"><a href="/">Lazavel❣</a></div>
            @if (! request()->is('checkout'))
            <ul>
                @if (session()->has('user'))
                @if ((array_key_exists("url",session()->get('user'))))
                <li><a href="{{ $url.'/api/setsession?user_id='.$user_id.'&session_id='.$session_id }}">Shop</a></li>
                @endif
                @else
                <li><a href="{{ route('shop.index') }}">Shop</a></li>
                @endif
                <li><a href="#">About</a></li>
                <li><a href="#">Blog</a></li>
                {{-- <li>
                        <a href="{{ route('cart.index') }}">Cart <span class="cart-count">
                            @if (Cart::instance('default')->count() > 0)
                            <span>{{ Cart::instance('default')->count() }}</span></span>
                            @endif
                        </a>
                </li>  --}}
            </ul>
            @endif
        </div>
        <div class="top-nav-right">
            @if (! request()->is('checkout'))
                @include('partials.menus.main-right')
            @endif
        </div>
    </div> <!-- end top-nav -->
</header>
