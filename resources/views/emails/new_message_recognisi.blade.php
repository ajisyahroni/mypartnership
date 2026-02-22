<!DOCTYPE html>
<html>

<head>
    <title>{{ @$judulPesan }}</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table align="center" width="100%" cellspacing="0" cellpadding="0"
        style="max-width: 600px; background: #ffffff; border-radius: 10px; padding: 20px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
        <tr>
            <td align="center" style="padding-bottom: 20px;">
                <h2 style="color: #333;">📩 {{ @$judulPesan }}</h2>
            </td>
        </tr>

        <tr>
            <td>
                <p style="font-size: 16px; color: #666;">Halo <strong>{{ @$nama_receiver }}</strong>,</p>
                <p style="font-size: 16px; color: #666;">Kamu mendapatkan pesan dari
                    <strong>{{ @$nama_sender }}</strong>:
                </p>
                <p style="font-size: 16px; color: #666;">
                        terdapat chat baru data rekognisi pada departement <b>{{ $department }}</b> atas nama <b>{{ $nama_prof }}</b>. mohon membuka sistem rekognisi untuk melihatnya. <br>

                        ---------------------------------------------------------------------------------------------------------<br>

                        There is a new chat in the recognition data in department {{ $department }} under the name {{ $nama_prof }}. Please open the recognition system to view it.<br>
                </p>

                <blockquote style="border-left: 4px solid #007bff; padding-left: 10px; font-size: 16px; color: #444;">
                    {{ @$chat }}
                </blockquote>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding-top: 20px;">
                <a href="{{ @$url_chat }}"
                    style="background: #007bff; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">
                    🔗 Buka Pesan
                </a>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding-top: 20px; font-size: 14px; color: #999;">
                <p>Jangan balas email ini. Jika kamu ingin membalas pesan, klik tombol di atas.</p>
                <p style="margin-top: 10px;">© {{ date('Y') }} MyPartnership. All Rights Reserved.</p>
            </td>
        </tr>
    </table>

</body>

</html>
