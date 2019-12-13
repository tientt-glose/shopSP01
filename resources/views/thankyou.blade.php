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
       @if ($url==null)
            <a href="{{ url('/') }}" class="button">Home Page</a>
       @else
            <a href="{{ $url.'/setsession?user_id='.$user_id.'&session_id='.$session_id }}" class="button">Home Page</a>
       @endif
       <div class="spacer"></div>
   </div>




@endsection
