@extends('layout')

@section('title', 'Shopping Cart')

@section('extra-css')

@endsection

@section('content')

    <div class="breadcrumbs">
        <div class="container">
            <a href="/">Home</a>
            <i class="fa fa-chevron-right breadcrumb-separator"></i>
            <span>Shopping Cart</span>
        </div>
    </div> <!-- end breadcrumbs -->

    <div class="cart-section container">
        <div>
            @if (session()->has('success_message'))
                <div class="alert alert-success">
                    {{ session()->get('success_message') }}
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (getQuantitybyCartProduct($cartproduct) > 0)

            <h2>{{ getQuantitybyCartProduct($cartproduct) }} item(s) in Shopping Cart</h2>

            <div class="cart-table">
                @foreach ($cartproduct as $item)
                <div class="cart-table-row">
                    <div class="cart-table-row-left">
                        <a href="{{ route('shop.show', $item->product_id) }}"><img src="{{ $item->getImgById() }}" alt="item" class="cart-table-img"></a>
                        <div class="cart-item-details">
                            <div class="cart-table-item"><a href="{{ route('shop.show', $item->product_id) }}">{{ $item->name }}</a></div>
                            <div class="cart-table-description">{{ $item->getDesById() }}</div>
                        </div>
                    </div>
                    <div class="cart-table-row-right">
                        <div class="cart-table-actions">
                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" clas="cart-options">Remove</button>
                            </form>
                        </div>
                        <div>
                            <select class="quantity" data-id="{{ $item->id }}">
                                @for ($i = 1; $i <= 10 ; $i++)
                                    <option {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        {{-- <div>{{ presentPrice($item->getProductTotalById()) }}</div> --}}
                        <div>{{ presentPrice($item->price) }}</div>
                    </div>
                </div> <!-- end cart-table-row -->
                @endforeach
            </div> <!-- end cart-table -->

            <div class="cart-totals">
                <div class="cart-totals-left">
                    Subtotal <br>
                    Tax (10%) <br>
                    Ship <br>
                    <span class="cart-totals-total">Total</span>
                </div>

                <div class="cart-totals-right">
                    <div class="cart-totals-subtotal">
                        {{ presentPrice(getSubTotal($cartproduct)) }} <br>
                        {{ presentPrice(getTax($cartproduct)) }} <br>
                        {{ presentPrice(config('app.ship')) }} <br>
                        <span class="cart-totals-total">{{ presentPrice(getTotal($cartproduct)) }}</span>
                    </div>
                </div>
            </div> <!-- end cart-totals -->

            <div class="cart-buttons">
                @if ($url==null)
                <a href="{{ route('shop.index') }}" class="button">Continue Shopping</a>
                @else
                <a href="{{ $url.'/api/setsession?user_id='.$user_id.'&session_id='.$session_id }}" class="button">Continue Shopping</a>
                @endif
                <a href="{{ route('checkout.index') }}" class="button-primary">Proceed to Checkout</a>
            </div>
            @else
                <h3>No item in Cart!</h3>
                <div class="spacer"></div>
                @if ($url==null)
                <a href="{{ route('shop.index') }}" class="button">Continue Shopping</a>
                @else
                <a href="{{ $url.'/api/setsession?user_id='.$user_id.'&session_id='.$session_id }}" class="button">Continue Shopping</a>
                @endif
                <div class="spacer"></div>
            @endif 

        </div>

    </div> <!-- end cart-section -->

    @include('partials.might-like')


@endsection

@section('extra-js')
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function(){
            const classname = document.querySelectorAll('.quantity')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    axios.patch(`cart/${id}`, {
                        quantity: this.value
                    })
                    .then(function (response) {
                        // console.log(response);
                        window.location.href = '{{ route('cart.index') }}'
                    })
                    .catch(function (error) {
                        // console.log(error);
                        window.location.href = '{{ route('cart.index') }}'
                    });
                })
            })
        })();
    </script>
@endsection

