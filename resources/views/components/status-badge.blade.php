@props(['event'])
<span {{ $attributes->merge([
  'class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . $event->status_badge_classes
]) }}>
  {{ $event->status }}
</span>
