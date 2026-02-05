<tr>
    <td>
        <table class="footer" width="570" style="width: 570px; margin: 0 auto; padding: 0; text-align: center;"
               role="presentation">
            <tr>
                <td class="content-cell" style="max-width: 100vw; padding: 32px; text-align: center;">
                    {!! Illuminate\Mail\Markdown::parse($slot) !!}
                </td>
            </tr>
        </table>
    </td>
</tr>
