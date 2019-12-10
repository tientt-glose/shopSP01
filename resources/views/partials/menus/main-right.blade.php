<ul>
    @if (!(session()->has('user')))
    <li><a href="{{ route('register') }}">Sign Up</a></li>
    <li><a href="{{ route('login') }}">Login</a></li>
    @else
    <li>
        <a href="{{ config('app.auth').'/logout?url='.config('app.api') }}">
            Logout
        </a>
    </li>
    @endif
    <li><a href="{{ route('cart.index') }}">Cart
    @if (getQuantity() > 0)
    <span class="cart-count"><span>{{ getQuantity() }}</span></span>
    @endif
    </a></li>

</ul>