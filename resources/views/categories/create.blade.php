@extends('layouts.app')

@section('content')
    <h1>Create a New Category</h1>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="name">Category Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="image">Category Image:</label>
        <input type="file" name="image" id="image">

        <button type="submit">Create Category</button>
    </form>
@endsection
