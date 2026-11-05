@props([
    'permission',
    'href' => '#',
    'class' => 'btn btn-primary',
    'target' => null,
])
@can($permission)
    <a href="{{ $href }}" class="{{ $class }}" @if($target) target="{{ $target }}" @endif {{ $attributes }}>
        {{ $slot }}
    </a>
@endcan
