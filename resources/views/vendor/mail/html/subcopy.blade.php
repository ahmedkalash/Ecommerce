<table class="subcopy" width="100%"
       style="width: 100%; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;" role="presentation">
    <tr>
        <td>
            {!! Illuminate\Mail\Markdown::parse($slot) !!}
        </td>
    </tr>
</table>
