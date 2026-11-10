@php $n = $name ?? 'circle'; $c = $cls ?? 'h-6 w-6'; @endphp
@if(str_starts_with($n, 'bi-'))<i class="bi {{ $n }}" aria-hidden="true"></i>@else<i data-lucide="{{ $n }}" class="{{ $c }}" aria-hidden="true"></i>@endif
