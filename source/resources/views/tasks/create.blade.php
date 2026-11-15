@extends('layouts.tasks')
@section('content')<h1>Create task</h1><form class="panel" method="POST" action="{{ route('tasks.store') }}">@include('tasks.form')</form>@endsection
