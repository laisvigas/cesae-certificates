@props(['event'])

<span @class([
  'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
  'bg-gray-100 text-gray-700' => $event->status === 'Encerrado',
  'bg-blue-100 text-blue-800' => $event->status === 'Agendado',
  'bg-green-100 text-green-800' => $event->status === 'A decorrer',
])>
  {{ $event->status }}
</span>
