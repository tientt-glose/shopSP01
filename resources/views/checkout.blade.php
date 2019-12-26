@extends('layout')

@section('title', 'Checkout')

@section('extra-css')

<script src="https://js.stripe.com/v3/"></script>

@endsection

@section('content')

    <div class="container">

        @if (session()->has('success_message'))
            <div class="spacer"></div>
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if(count($errors) > 0)
            <div class="spacer"></div>
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="checkout-heading stylish-heading">Checkout</h1>
        <div class="checkout-section">
            <div>
                <form action="{{ route('checkout.store') }}" method="POST" id="payment-form">
                    {{ csrf_field() }}
                    <h2>Billing Details</h2>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                    </div>

                    <div class="half-form">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province" value="{{ old('province') }}" required>
                        </div>
                    </div> <!-- end half-form -->

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                    </div>

                    <div class="spacer"></div>

                    <h2>Delivery Units</h2>
                    <div class="form-group">
                        <select class="quantity" data-id={{ session()->get('delivery')['id'] }}>
                            @for ($i = 0; $i < count($delivery); $i++)
                                <option 
                                    {{ session()->get('delivery')['id'] == $i ? 'selected' : '' }}
                                    value="{{ $i }}" >{{ $delivery[$i]['name'].' - '.$delivery[$i]['base_fee'].'VND'}}
                                </option>
                            @endfor
                        </select>
                    </div>


                    <div class="spacer"></div>
                    
                    <h2>Payment Details</h2>

                    <div class="form-group">
                        <select class="payment" data-id={{ session()->get('user')['user_id'] }}>
                            @if ($payment != null)
                            @foreach ($payment as $pay)
                                <option 
                                    {{ session()->get('payment')['card_id'] == $pay['card_id'] ? 'selected' : '' }}
                                    value="{{ $pay['card_id'] }}">
                                {{ substr($pay['card_number'],0,7).'*********'}}</option>
                            @endforeach
                            @endif
                            <option {{ session()->get('payment')['type'] == "COD" ? 'selected' : '' }} value="COD">COD</option>
                            <option {{ ((session()->get('payment')['type'] == "Card") && (session()->get('payment')['card_id'] == null) ) ? 'selected' : '' }} value="Other">Other ...</option>
                        </select>
                    </div>

                    @if ((session()->get('payment')['type'] == "Card") && (session()->get('payment')['card_id'] == null))
                    <div class="form-group">
                        <label for="name_on_card">Name on Card</label>
                        <input type="text" class="form-control" id="name_on_card" name="name_on_card" value="" required>
                    </div>

                    <div class="form-group">
                        <label for="ccnumber">Credit Card Number</label>
                        <input type="text" class="form-control" id="ccnumber" name="ccnumber" value="" required>
                    </div>

                    <div class="half-form">
                        <div class="form-group">
                            <label for="expiry">Expiry</label>
                            <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/DD" required>
                        </div>
                        <div class="form-group">
                            <label for="cvc">CVC Code</label>
                            <input type="text" class="form-control" id="cvc" name="cvc" value="" required>
                        </div>
                    </div> <!-- end half-form -->
                    @endif
                    
                    <div class="spacer"></div>

                    <button type="submit" id="complete-order" class="button-primary full-width">Complete Order</button>


                </form>
            </div>



            <div class="checkout-table-container">
                <h2>Your Order</h2>

                
                <div class="checkout-table">
                        @foreach ($cartproduct as $item)
                        <div class="checkout-table-row">
                            <div class="checkout-table-row-left">
                                <img src="{{ $item->getImgById() }}" alt="item" class="checkout-table-img">
                                <div class="checkout-item-details">
                                    <div class="checkout-table-item">{{ $item->name }}</div>
                                    <div class="checkout-table-description">{{ $item->getDesById() }}</div>
                                    {{-- <div class="checkout-table-price">{{ presentPrice($item->getProductTotalById()) }}</div> --}}
                                    <div class="checkout-table-price">{{ presentPrice($item->price) }}</div>

                                </div>
                            </div> <!-- end checkout-sub-table -->
    
                            <div class="checkout-table-row-right">
                                <div class="checkout-table-quantity">{{ $item->quantity }}</div>
                            </div>
                        </div> <!-- end checkout-table-row -->
                        @endforeach
                </div> <!-- end checkout-table -->

                <div class="checkout-totals">
                    <div class="checkout-totals-left">
                        Subtotal <br>
                        @if (session()->has('coupon'))
                            Discount ({{ session()->get('coupon')['code'] }}) :
                            <form action="{{ route('coupon.destroy') }}" method="POST" style="display:inline">
                                {{ csrf_field() }}
                                {{ method_field('delete') }}
                                <button type="submit" style="font-size:14px">Remove</button>
                            </form>
                            <br>
                            <hr>
                            New Subtotal <br>
                        @endif
                        Tax (10%) <br>
                        Ship <br>
                        <span class="checkout-totals-total">Total</span>

                    </div>

                    <div class="checkout-totals-right">
                        {{ presentPrice(getSubTotal($cartproduct)) }} <br>
                        @if (session()->has('coupon'))
                            -{{ presentPrice($discount) }} <br>
                            <hr>
                            {{ presentPrice($newSubtotal) }} <br>
                        @endif
                        {{ presentPrice($newTax) }} <br>
                        {{ presentPrice($shipping) }} <br>
                        <span class="checkout-totals-total">{{ presentPrice($newTotal) }}</span>

                    </div>
                </div> <!-- end checkout-totals -->
                @if (! session()->has('coupon'))
                <a href="#" class="have-code">Have a Code?</a>

                <div class="have-code-container">
                    <form action="{{ route('coupon.store') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="text" name="coupon_code" id="coupon_code">
                        <button type="submit" class="button button-plain">Apply</button>
                    </form>
                </div> <!-- end have-code-container -->
                @endif

            </div>

        </div> <!-- end checkout-section -->
    </div>

@endsection

@section('extra-js')

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function(){
            const classname = document.querySelectorAll('.quantity')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    axios.patch(`checkout_delivery/${id}`, {
                        delivery_id: this.value
                    })
                    .then(function (response) {
                        // console.log(response);
                        window.location.href = '{{ route('checkout.index') }}'
                    })
                    .catch(function (error) {
                        // console.log(error);
                        window.location.href = '{{ route('checkout.index') }}'
                    });
                })
            })
        })();
        
        (function(){
            const classname = document.querySelectorAll('.payment')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    axios.patch(`checkout_payment/${id}`, {
                        type: this.value
                    })
                    .then(function (response) {
                        // console.log(response);
                        window.location.href = '{{ route('checkout.index') }}'
                    })
                    .catch(function (error) {
                        console.log(error);
                        window.location.href = '{{ route('checkout.index') }}'
                    });
                })
            })
        })();
    </script>
    
@endsection

