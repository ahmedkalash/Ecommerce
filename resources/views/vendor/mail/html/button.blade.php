@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" width="100%" style="width: 100%; margin: 30px auto; padding: 0; text-align: {{ $align }};"
       role="presentation">
    <tr>
        <td style="text-align: {{ $align }};">
            <table width="100%" style="width: 100%;" role="presentation">
                <tr>
                    <td style="text-align: {{ $align }};">
                        <table style="margin: 0 auto;" role="presentation">
                            <tr>
                                <td>
                                    <a href="{{ $url }}" class="button button-{{ $color }}" target="_blank"
                                       rel="noopener"
                                       style="-webkit-text-size-adjust: none; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none;">{{ $slot }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
