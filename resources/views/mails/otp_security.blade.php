<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logohelix.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Keamanan - Helix</title>
</head>
<body style="font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 40px 20px; color: #1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
        <!-- Header -->
        <tr>
            <td style="background-color: #dc2626; padding: 40px 30px; text-align: center;">
                <img src="{{ url('logohelix.png') }}" alt="Helix Logo" height="40" style="display: block; border: 0;">
                <p style="color: #fecaca; margin: 10px 0 0 0; font-size: 14px; font-weight: 500; letter-spacing: 2px; text-transform: uppercase;">Security Alert</p>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <h2 style="margin: 0 0 20px 0; font-size: 22px; font-weight: 800; color: #111827;">Halo {{ $userName }},</h2>
                <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 1.6; color: #4b5563;">
                    Kami menerima permintaan untuk melakukan <strong>perubahan pada keamanan akun Anda (Ubah Sandi)</strong>. Untuk melanjutkan, silakan masukkan kode verifikasi (OTP) berikut:
                </p>
                
                <!-- OTP Box -->
                <div style="background-color: #fef2f2; border: 2px dashed #fca5a5; border-radius: 16px; padding: 30px 20px; text-align: center; margin-bottom: 30px;">
                    <div style="font-size: 48px; font-weight: 900; letter-spacing: 16px; color: #dc2626; margin-left: 16px;">
                        {{ $otpCode }}
                    </div>
                </div>
                
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #fef2f2; border-radius: 12px; padding: 15px;">
                    <tr>
                        <td width="30" style="vertical-align: top; padding-right: 10px; color: #ef4444; font-size: 20px;">
                            &#9888;
                        </td>
                        <td style="font-size: 13px; line-height: 1.5; color: #991b1b;">
                            <strong>Perhatian:</strong> Kode ini hanya berlaku selama <strong style="color: #dc2626;">5 menit</strong>. Jika Anda <strong>tidak pernah meminta</strong> perubahan sandi ini, segera abaikan email ini dan pastikan akun Anda tetap aman.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #f3f4f6;">
                <p style="margin: 0 0 10px 0; font-size: 12px; color: #9ca3af;">
                    Email ini dikirimkan otomatis dari sistem keamanan Helix.
                </p>
                <p style="margin: 0; font-size: 12px; font-weight: 700; color: #6b7280;">
                    &copy; {{ date('Y') }} Helix Enterprise.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
