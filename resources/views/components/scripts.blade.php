@props(['files' => []])

@foreach ($files as $file)
    <script src="{{ \Str::startsWith($file, ['http://', 'https://']) ? $file : asset($file) }}"></script>
@endforeach
