@props(['files' => []])

@foreach ($files as $file)
    <link rel="stylesheet" href="{{ \Str::startsWith($file, ['http://', 'https://']) ? $file : asset($file) }}">
@endforeach
