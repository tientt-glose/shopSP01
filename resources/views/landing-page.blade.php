<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Lazavel❣</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat%7CRoboto:300,400,700" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    </head>
    <body>
        <header class="with-background">
            <div class="top-nav container">
                <div class="top-nav-left">
                    <div class="logo">Lazavel❣</div>
                    <ul>
                        <li><a href="{{ route('shop.index') }}">Shop</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Sale</a></li>
                        {{-- <li>
                                <a href="{{ route('cart.index') }}">Cart <span class="cart-count">
                                    @if (Cart::instance('default')->count() > 0)
                                    <span>{{ Cart::instance('default')->count() }}</span></span>
                                    @endif
                                </a>
                        </li>  --}}
                    </ul>
                </div>

                <div class="top-nav-right">
                    @include('partials.menus.main-right')
                </div>
                
            </div> <!-- end top-nav -->
            <div class="hero container">
                <div class="hero-copy">
                    <h1>Lazavel 💗</h1>
                    <h2>Southeast Asia's No. 1 Online Shopping</h2> 
                    <p>With 300 million SKUs available, Lazavel❣ offers the widest range of products in categories from beauty, fashion 
                        and consumer electronics to household goods, toys, sports equipment and groceries.</p>
                    <div class="hero-buttons">
                        <a href="#" class="button button-white">More...</a>
                        <a href="#" class="button button-white">Sell on Lazavel❣</a>
                    </div>
                </div> <!-- end hero-copy -->

                <div class="hero-image">
                    <img src="img/macbook-pro-laravel.png" alt="hero image">
                </div> <!-- end hero-image -->
            </div> <!-- end hero -->
        </header>

        <div class="featured-section">

            <div class="container">
                <h1 class="text-center">Discover Lazavel❣</h1>

                <p class="text-center section-description">It's an "ADD TO CART" kinda day! <br> Happiness is ... <br> Receiving what you ORDERED online!</p>

                <div class="text-center button-container">
                    <a href="#" class="button">Featured</a>
                    <a href="#" class="button">On Sale</a>
                </div>

                <div class="products text-center">
                    @foreach ($products as $product)
                        <div class="product">
                            <a href="{{ route('shop.show', $product->id) }}"><img src="{{ $product->image }}" alt="product"></a>
                            <a href="{{ route('shop.show', $product->id) }}"><div class="product-name">{{ $product->name }}</div></a>
                            <div class="product-price">{{ $product->presentPrice() }}</div>
                        </div>
                    @endforeach

                </div> <!-- end products -->

                <div class="text-center button-container">
                    <a href="{{ route('shop.index') }}" class="button">View more products</a>
                </div>

            </div> <!-- end container -->

        </div> <!-- end featured-section -->

        <div class="blog-section">
            <div class="container">
                <h1 class="text-center">Don't Miss Out</h1>

                <p class="text-center section-description">Nothing happens until a SALE is made! Check It Out!</p>
                <div class="blog-posts">
                    <div class="blog-post" id="blog1">
                        <a href="#"><img src="https://images-na.ssl-images-amazon.com/images/G/01/US-hq/2019/img/Events/XCM_Manual_1191967_1191967_us_events_holideals_dashboard_1x_379x304_5_1570473588_jpg._SY304_CB451536217_.jpg" alt="Blog Image"></a>
                        <a href="#"><h2 class="blog-title">Happy Holiday Deals</h2></a>
                        <div class="blog-description">All you need for enjoying holiday.</div>
                    </div>
                    <div class="blog-post" id="blog2">
                        <a href="#"><img src="https://laz-img-cdn.alicdn.com/images/ims-web/TB1WMaJm.T1gK0jSZFhXXaAtVXa.jpg_1200x1200q75.jpg_.webp" alt="Blog Image"></a>
                        <a href="#"><h2 class="blog-title">Everything about Marvel!</h2></a>
                        <div class="blog-description">Shop to win Frozen 2 merchandise.</div>
                    </div>
                    <div class="blog-post" id="blog3">
                        <a href="#"><img src="https://laz-img-cdn.alicdn.com/images/ims-web/TB18kPuni_1gK0jSZFqXXcpaXXa.jpg_720x720Q100.jpg_.webp" alt="Blog Image"></a>
                        <a href="#"><h2 class="blog-title">up to 15% OFF with HSBC credit cards</h2></a>
                        <div class="blog-description">For festive feasting preparation.</div>
                    </div>
                </div>
            </div> <!-- end container -->
        </div> <!-- end blog-section -->

        @include('partials.footer')


    </body>
</html>
