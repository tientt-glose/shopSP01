@extends('layout')

@section('title', 'Thank You')

@section('extra-css')

@endsection

@section('body-class', 'sticky-footer')

@section('content')

   <div class="thank-you-section">
       <h1>Thank you for <br> Your Order!</h1>
       <p>A confirmation email was sent</p>
       <div class="spacer"></div>
        @if (!(array_key_exists("url",session()->get('user'))))
            <a href="{{ url('/') }}" class="button">Home Page</a>
        @else
            <a href="{{ session()->get('user')['url'] . '/api/setsession?user_id=' . session()->get('user')['user_id'] . '&session_id=' . session()->get('user')['session_id'] }}" class="button">Home Page</a>
        @endif
       <div class="spacer"></div>
       <a href="{{ config('app.nhom4'). '/auth/setsession?user_id=' . session()->get('user')['user_id'] . '&session_id=' . session()->get('user')['session_id'] }}" class="button">Your Orders</a>
   </div>
@endsection
