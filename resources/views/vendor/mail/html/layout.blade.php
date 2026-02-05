<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset styles */
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        td {
            padding: 0;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; width: 100%; background-color: #edf2f7;">

<table class="wrapper" width="100%" style="width: 100%; margin: 0; padding: 0; background-color: #edf2f7;"
       role="presentation">
    <tr>
        <td style="text-align: center;">
            <table class="content" width="100%" style="width: 100%; margin: 0; padding: 0;" role="presentation">
                {!! $header ?? '' !!}

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%"
                        style="width: 100%; background-color: #edf2f7; border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; margin: 0; padding: 0;">
                        <table class="inner-body" width="570"
                               style="width: 570px; margin: 0 auto; padding: 0; background-color: #ffffff; border-radius: 2px; border: 1px solid #e8e5ef; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);"
                               role="presentation">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell" style="max-width: 100vw; padding: 32px;">
                                    {!! $slot !!}

                                    {!! $subcopy ?? '' !!}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {!! $footer ?? '' !!}
            </table>
        </td>
    </tr>
</table>
</body>
</html>
