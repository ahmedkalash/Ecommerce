<table class="panel" width="100%" style="width: 100%; border-left: 4px solid #2d3748; margin: 21px 0;"
       role="presentation">
    <tr>
        <td class="panel-content" style="background-color: #edf2f7; color: #718096; padding: 16px;">
            <table width="100%" style="width: 100%;" role="presentation">
                <tr>
                    <td class="panel-item" style="padding: 0;">
                        {!! Illuminate\Mail\Markdown::parse($slot) !!}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
