<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', $setting?->website_name ?? 'Dela Moure')
    </title>

    <style>
        @media only screen and (max-width: 680px) {
            .email-wrapper {
                padding: 8px 4px !important;
            }

            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .email-header {
                padding: 16px 18px !important;
            }

            .email-hero {
                padding: 22px 18px !important;
            }

            .email-content {
                padding: 22px 18px 18px !important;
            }

            .email-footer {
                padding: 20px 18px !important;
            }

            .email-title {
                font-size: 24px !important;
            }

            .promise-text {
                font-size: 11px !important;
            }
        }
    </style>
</head>

<body
    style="
        margin:0;
        padding:0;
        background:#f3f1ed;
        font-family:Arial, Helvetica, sans-serif;
        color:#333333;
    ">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper"
        style="
            width:100%;
            background:#f3f1ed;
            padding:12px 6px;
        ">

        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0" border="0" class="email-container"
                    style="
                        width:100%;
                        max-width:650px;
                        background:#ffffff;
                        border-radius:10px;
                        overflow:hidden;
                    ">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center" class="email-header"
                            style="
                                background:#4A2C20;
                                padding:16px 24px;
                            ">

                            <img src="https://shopdelamoure.com/assets/images/email/logo.png"
                                alt="{{ $setting?->website_name ?? 'Dela Moure' }}" width="179"
                                style="
                                    width:179px;
                                    max-width:179px;
                                    height:auto;
                                    display:block;
                                    margin:0 auto;
                                    border:0;
                                    outline:none;
                                    text-decoration:none;
                                ">

                        </td>
                    </tr>


                    {{-- GOLD ACCENT --}}
                    <tr>
                        <td
                            style="
                                height:4px;
                                background:#C9A227;
                                font-size:0;
                                line-height:0;
                            ">
                            &nbsp;
                        </td>
                    </tr>


                    {{-- HERO --}}
                    <tr>
                        <td class="email-hero"
                            style="
                                background:#6B3F2A;
                                padding:24px 28px;
                                text-align:center;
                            ">

                            <div
                                style="
                                    color:#E3C76F;
                                    font-size:11px;
                                    font-weight:700;
                                    text-transform:uppercase;
                                    letter-spacing:2px;
                                    margin-bottom:7px;
                                ">
                                @yield('eyebrow', 'Dela Moure')
                            </div>

                            <h1 class="email-title"
                                style="
                                    margin:0;
                                    color:#ffffff;
                                    font-size:26px;
                                    line-height:1.3;
                                    font-weight:700;
                                ">
                                @yield('heading')
                            </h1>

                            @hasSection('subheading')
                                <p
                                    style="
                                        margin:9px auto 0;
                                        max-width:500px;
                                        color:#f3e8df;
                                        font-size:14px;
                                        line-height:1.55;
                                    ">
                                    @yield('subheading')
                                </p>
                            @endif

                        </td>
                    </tr>


                    {{-- CONTENT --}}
                    <tr>
                        <td class="email-content"
                            style="
                                padding:24px 28px 20px;
                            ">

                            @yield('content')

                        </td>
                    </tr>


                    {{-- CUSTOMER PROMISE --}}
                    <tr>
                        <td
                            style="
                                background:#F8F4EC;
                                padding:14px 12px;
                                border-top:1px solid #eee6da;
                            ">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>

                                    <td width="33.33%" align="center" valign="top" style="padding:5px 3px;">

                                        <div
                                            style="
                                                color:#C9A227;
                                                font-size:19px;
                                                line-height:1;
                                                margin-bottom:5px;
                                            ">
                                            ✓
                                        </div>

                                        <div class="promise-text"
                                            style="
                                                color:#4A2C20;
                                                font-size:12px;
                                                font-weight:700;
                                            ">
                                            Quality Assured
                                        </div>

                                    </td>


                                    <td width="33.33%" align="center" valign="top" style="padding:5px 3px;">

                                        <div
                                            style="
                                                color:#C9A227;
                                                font-size:19px;
                                                line-height:1;
                                                margin-bottom:5px;
                                            ">
                                            ◇
                                        </div>

                                        <div class="promise-text"
                                            style="
                                                color:#4A2C20;
                                                font-size:12px;
                                                font-weight:700;
                                            ">
                                            Secure Shopping
                                        </div>

                                    </td>


                                    <td width="33.33%" align="center" valign="top" style="padding:5px 3px;">

                                        <div
                                            style="
                                                color:#C9A227;
                                                font-size:19px;
                                                line-height:1;
                                                margin-bottom:5px;
                                            ">
                                            ♡
                                        </div>

                                        <div class="promise-text"
                                            style="
                                                color:#4A2C20;
                                                font-size:12px;
                                                font-weight:700;
                                            ">
                                            Customer Care
                                        </div>

                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>


                    {{-- FOOTER LINKS --}}
                    <tr>
                        <td align="center"
                            style="
                                background:#6B3F2A;
                                padding:12px 16px;
                            ">

                            <a href="{{ url('/') }}"
                                style="
                                    color:#ffffff;
                                    text-decoration:none;
                                    font-size:12px;
                                    margin:0 8px;
                                ">
                                Shop
                            </a>

                            <a href="{{ url('/about-us') }}"
                                style="
                                    color:#ffffff;
                                    text-decoration:none;
                                    font-size:12px;
                                    margin:0 8px;
                                ">
                                About Us
                            </a>

                            <a href="{{ url('/contact-us') }}"
                                style="
                                    color:#ffffff;
                                    text-decoration:none;
                                    font-size:12px;
                                    margin:0 8px;
                                ">
                                Contact
                            </a>

                        </td>
                    </tr>


                    {{-- FOOTER --}}
                    <tr>
                        <td class="email-footer"
                            style="
                                background:#4A2C20;
                                padding:20px 24px;
                                color:#e7dcd5;
                            ">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>

                                    <td valign="top"
                                        style="
                                            font-size:11px;
                                            line-height:1.6;
                                        ">

                                        <strong
                                            style="
                                                color:#C9A227;
                                                font-size:13px;
                                            ">
                                            {{ $setting?->website_name ?? 'Dela Moure' }}
                                        </strong>

                                        @if (!empty($setting?->address))
                                            <div style="margin-top:5px;">
                                                {{ $setting->address }}
                                            </div>
                                        @endif

                                        @if (!empty($setting?->email))
                                            <div>
                                                {{ $setting->email }}
                                            </div>
                                        @endif

                                        @if (!empty($setting?->phone))
                                            <div>
                                                {{ $setting->phone }}
                                            </div>
                                        @endif

                                    </td>

                                </tr>
                            </table>


                            {{-- SOCIAL LINKS --}}
                            @if (!empty($setting?->instagram) || !empty($setting?->facebook) || !empty($setting?->tiktok))
                                <div
                                    style="
                                        margin-top:12px;
                                        padding-top:11px;
                                        border-top:1px solid rgba(255,255,255,.15);
                                    ">

                                    @if (!empty($setting?->instagram))
                                        <a href="{{ $setting->instagram }}"
                                            style="
                                                color:#C9A227;
                                                margin-right:10px;
                                                text-decoration:none;
                                                font-size:11px;
                                            ">
                                            Instagram
                                        </a>
                                    @endif

                                    @if (!empty($setting?->facebook))
                                        <a href="{{ $setting->facebook }}"
                                            style="
                                                color:#C9A227;
                                                margin-right:10px;
                                                text-decoration:none;
                                                font-size:11px;
                                            ">
                                            Facebook
                                        </a>
                                    @endif

                                    @if (!empty($setting?->tiktok))
                                        <a href="{{ $setting->tiktok }}"
                                            style="
                                                color:#C9A227;
                                                margin-right:10px;
                                                text-decoration:none;
                                                font-size:11px;
                                            ">
                                            TikTok
                                        </a>
                                    @endif

                                </div>
                            @endif


                            <p
                                style="
                                    margin:12px 0 0;
                                    font-size:10px;
                                    line-height:1.5;
                                    color:#bfaea4;
                                ">
                                © {{ date('Y') }}
                                {{ $setting?->website_name ?? 'Dela Moure' }}.
                                All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>

</html>
