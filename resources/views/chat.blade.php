@extends('layouts.app')

@section('content')

<div class="container h-100">
    <div class="row h-100">
        <div class="col-md-12 col-md-offset-2">
            <div class="panel panel-default h-100">

                <div class="panel-body h-100">
                    <chat-messages :loginuser="{{ Auth::user() }}"></chat-messages>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection