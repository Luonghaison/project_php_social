@component('mail::message')
    # Xác thực OTP

    Cảm ơn bạn đã đăng ký tài khoản JobMatching

    Hãy nhập OTP đưới đây vào màn hình xác thực OTP để hoàn thành việc đăng ký.

    One Time Password (OTP): {{ $otp_code }}
@endcomponent


