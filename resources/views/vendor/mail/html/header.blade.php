@props(['url'])
<tr>
    <td class="header" style="padding: 25px 0; text-align: center;">
        <a href="{{ $url }}"
           style="display: inline-block; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"
                     style="height: 75px; max-height: 75px; width: 75px;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
