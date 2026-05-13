<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Добродошлица</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

                    <!-- Decorative Folk Header with Embedded Logo -->
                    <tr>
                        <td style="padding:0; background:#ffffff;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    @for ($i = 0; $i < 15; $i++)
                                        <td width="20" height="20" style="background:#c40000; font-size:0; line-height:0;">&nbsp;</td>
                                        <td width="20" height="20" style="background:#0033a0; font-size:0; line-height:0;">&nbsp;</td>
                                    @endfor
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="center" style="padding:20px 0;">
                                        <img src="{{ $message->embed(public_path('images/logo.png')) }}"
                                             alt="КУД Српски Вез" width="120"
                                             style="display:block; width:120px; height:auto; border:0; outline:none;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">

                            <h1 style="font-size:28px; color:#313131; margin:0; text-align:center;">
                                Здраво, {{ $member->full_name }}!
                            </h1>

                            <p style="font-size:16px; color:#444444; line-height:1.6;">
                                Добродошли на нашу платформу. Радујемо се што сте постали део наше заједнице!
                            </p>

                            <p style="font-size:16px; color:#444444; line-height:1.6;">
                                Ова платформа служи за управљање трансакцијама одеће, чланством, организовање догађаја, као и за пружање нових информација у вези са КУД „Српски Вез“.
                            </p>

                            <p style="font-size:16px; color:#444444; line-height:1.6;">
                                Надамо се да ћете уживати користећи платформу и да ће вам она бити од користи!
                            </p>

                            <div style="text-align:center; margin-top:25px;">
                                <a href="{{ config('app.url') }}"
                                   style="padding:12px 22px; background:#0051ff; color:white; text-decoration:none;
                                          border-radius:6px; font-size:16px; display:inline-block;">
                                    Посетите платформу
                                </a>
                            </div>

                            <p style="text-align:center; margin-top:40px; font-size:12px; color:#999;">
                                КУД „Српски Вез“ — Сва права задржана.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
