@extends('layouts.tasks')
@section('content')<h1>Edit task</h1><form class="panel" method="POST" action="{{ route('tasks.update', $task) }}">@method('PATCH')@include('tasks.form')</form>@endsection
