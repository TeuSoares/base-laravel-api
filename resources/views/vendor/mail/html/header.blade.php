@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    @if (file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png?v=1') }}" class="logo" alt="{{ config('app.name') }}">
    @else
        {!! $slot !!}
    @endif
</a>
</td>
</tr>
