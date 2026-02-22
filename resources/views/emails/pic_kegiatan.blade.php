<!DOCTYPE html>
<html>

<head>
    <title>{{ @$judulPesan ?? 'MyPartnership - Universitas Muhammadiyah Surakarta' }}</title>
</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; padding: 30px;">

    <table align="center" width="100%" cellspacing="0" cellpadding="0"
        style="max-width: 650px; background-color: #ffffff; border-radius: 10px; padding: 0; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);">

        <!-- Header -->
        <tr>
            <td align="center"
                style="background-color: #216aae; padding: 30px 20px 20px 20px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <img src="https://storage.googleapis.com/web_ums_object_storage/uploads/logo/hJSfcX9ZYUoft9d4Jp8n2nKiKlhHkUXskMz8vSq1.png"
                    alt="Logo UMS" style="width: 60px; margin-bottom: 15px;">
                <h2 style="color: #ffffff; margin: 0;">Universitas Muhammadiyah Surakarta</h2>
                <p style="color: #f0f0f0; font-size: 14px; margin: 5px 0 0;">MyPartnership Notification</p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 30px;">
                <h3 style="color: #333333; margin-bottom: 20px;"> {{ @$judulPesan ?? 'Pesan Baru' }}</h3>

                <div style="font-size: 14px; line-height: 1.7; color: #333;">
                    {!! @$dataMessage !!}
                </div>

                <div style="font-size: 13px; margin-top: 40px; color: #999;">
                    <p>Jangan membalas email ini. Jika Anda ingin merespon pesan, gunakan tombol di atas.</p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td align="center"
                style="background-color: #f7f7f7; padding: 20px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; font-size: 12px; color: #777;">
                Email ini dikirim secara otomatis oleh sistem <strong>MyPartnership</strong>.<br>
                Universitas Muhammadiyah Surakarta &copy; {{ date('Y') }}. All rights reserved.
            </td>
        </tr>

    </table>

</body>

</html>
