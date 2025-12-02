<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Верификација имејла</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

                    <!-- Decorative Folk Header with Embedded Logo -->
                    <tr>
                        <td style="padding:0; text-align:center; background:#ffffff;">
                            <svg width="100%" height="120" xmlns="http://www.w3.org/2000/svg" style="display:block; margin:0 auto;">
                                <!-- Pattern -->
                                <pattern id="folkRB" x="0" y="0" width="40" height="20" patternUnits="userSpaceOnUse">
                                    <rect width="40" height="20" fill="white"/>
                                    <!-- Red diamond -->
                                    <rect x="6" y="6" width="8" height="8" fill="#c40000"/>
                                    <rect x="8" y="8" width="4" height="4" fill="black"/>
                                    <!-- Blue diamond -->
                                    <rect x="26" y="6" width="8" height="8" fill="#0033a0"/>
                                    <rect x="28" y="8" width="4" height="4" fill="black"/>
                                </pattern>

                                <rect width="100%" height="120" fill="url(#folkRB)"/>

                                <!-- Logo centered on top -->
                                <image x="50%" y="5" width="150" height="150" style="transform: translateX(-50%);"
                                       href="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" />
                            </svg>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <h1 style="font-size:28px; color:#313131; margin:0; text-align:center;">
                                Верификација имејла
                            </h1>

                            <p style="font-size:16px; color:#444444; line-height:1.6; text-align:center;">
                                Молимо вас да кликнете на дугме испод како бисте верификовали вашу имејл адресу.
                            </p>

                            <div style="text-align:center; margin-top:25px;">
                                <a href="{{ $url }}"
                                   style="padding:12px 22px; background:#0051ff; color:white; text-decoration:none;
                                          border-radius:6px; font-size:16px; display:inline-block;">
                                    Верификуј имејл
                                </a>
                            </div>

                            <p style="font-size:14px; color:#666666; line-height:1.6; text-align:center; margin-top: 20px;">
                                Ако нисте креирали налог, није потребна никаква даља акција.
                            </p>

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
