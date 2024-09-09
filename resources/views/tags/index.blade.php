@extends('layouts.app')

@section('content')
    <h1>Tags</h1>

    <a href="{{ route('tags.create') }}">Create New Tag</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tags as $tag)
                <tr>
                    <td>{{ $tag->name }}</td>
                    <td><img src="{{ asset($tag->image) }}" alt="{{ $tag->name }}" width="100"></td>
                    <td>
                        <a href="{{ route('tags.edit', $tag->id) }}">Edit</a>
                        <form action="{{ route('tags.delete', $tag->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
