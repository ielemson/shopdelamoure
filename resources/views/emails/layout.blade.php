<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $setting?->website_name ?? 'Delamoure')</title>
</head>

<body
    style="
    margin:0;
    padding:0;
    background:#f3f1ed;
    font-family:Arial, Helvetica, sans-serif;
    color:#333333;
">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f1ed; padding:30px 10px;">

        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0" border="0"
                    style="
                    width:100%;
                    max-width:650px;
                    background:#ffffff;
                    border-radius:12px;
                    overflow:hidden;
                ">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center"
                            style="
                            background:#4A2C20;
                            padding:35px 30px;
                        ">

                            @if (!empty($setting?->logo))
                                <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->website_name }}"
                                    style="
                                    max-width:180px;
                                    max-height:65px;
                                    display:block;
                                ">
                            @else
                                <div
                                    style="
                                font-size:28px;
                                font-weight:700;
                                letter-spacing:2px;
                                color:#C9A227;
                            ">
                                    {{ strtoupper($setting?->website_name ?? 'DELAMOURE') }}
                                </div>
                            @endif

                        </td>
                    </tr>


                    {{-- GOLD STRIP --}}
                    <tr>
                        <td
                            style="
                        height:5px;
                        background:#C9A227;
                        font-size:0;
                        line-height:0;
                    ">
                            &nbsp;
                        </td>
                    </tr>


                    {{-- HERO --}}
                    <tr>
                        <td
                            style="
                        background:#6B3F2A;
                        padding:45px 45px;
                        text-align:center;
                    ">

                            <div
                                style="
                            color:#E3C76F;
                            font-size:13px;
                            font-weight:700;
                            text-transform:uppercase;
                            letter-spacing:2px;
                            margin-bottom:12px;
                        ">
                                @yield('eyebrow', 'Delamoure')
                            </div>

                            <h1
                                style="
                            margin:0;
                            color:#ffffff;
                            font-size:30px;
                            line-height:1.35;
                            font-weight:700;
                        ">
                                @yield('heading')
                            </h1>

                            @hasSection('subheading')
                                <p
                                    style="
                                margin:15px auto 0;
                                max-width:480px;
                                color:#f3e8df;
                                font-size:16px;
                                line-height:1.7;
                            ">
                                    @yield('subheading')
                                </p>
                            @endif

                        </td>
                    </tr>


                    {{-- CONTENT --}}
                    <tr>
                        <td style="padding:45px 45px 35px;">

                            @yield('content')

                        </td>
                    </tr>


                    {{-- CUSTOMER PROMISE --}}
                    <tr>
                        <td
                            style="
                        background:#F8F4EC;
                        padding:30px 25px;
                        border-top:1px solid #eee6da;
                    ">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>

                                    <td width="33.33%" align="center" valign="top" style="padding:10px;">

                                        <div
                                            style="
                                        font-size:26px;
                                        color:#C9A227;
                                        margin-bottom:10px;
                                    ">
                                            ✓
                                        </div>

                                        <div
                                            style="
                                        font-size:14px;
                                        font-weight:700;
                                        color:#4A2C20;
                                    ">
                                            Quality Assured
                                        </div>

                                    </td>


                                    <td width="33.33%" align="center" valign="top" style="padding:10px;">

                                        <div
                                            style="
                                        font-size:26px;
                                        color:#C9A227;
                                        margin-bottom:10px;
                                    ">
                                            ◇
                                        </div>

                                        <div
                                            style="
                                        font-size:14px;
                                        font-weight:700;
                                        color:#4A2C20;
                                    ">
                                            Secure Shopping
                                        </div>

                                    </td>


                                    <td width="33.33%" align="center" valign="top" style="padding:10px;">

                                        <div
                                            style="
                                        font-size:26px;
                                        color:#C9A227;
                                        margin-bottom:10px;
                                    ">
                                            ♡
                                        </div>

                                        <div
                                            style="
                                        font-size:14px;
                                        font-weight:700;
                                        color:#4A2C20;
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
                            padding:25px;
                        ">

                            <a href="{{ url('/') }}"
                                style="
                                color:#ffffff;
                                text-decoration:none;
                                font-size:13px;
                                margin:0 10px;
                            ">
                                Shop
                            </a>

                            <a href="{{ url('/about-us') }}"
                                style="
                                color:#ffffff;
                                text-decoration:none;
                                font-size:13px;
                                margin:0 10px;
                            ">
                                About Us
                            </a>

                            <a href="{{ url('/contact-us') }}"
                                style="
                                color:#ffffff;
                                text-decoration:none;
                                font-size:13px;
                                margin:0 10px;
                            ">
                                Contact
                            </a>

                        </td>
                    </tr>


                    {{-- FOOTER --}}
                    <tr>
                        <td
                            style="
                        background:#4A2C20;
                        padding:35px 40px;
                        color:#e7dcd5;
                    ">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>

                                    <td valign="top"
                                        style="
                                        font-size:13px;
                                        line-height:1.7;
                                    ">

                                        <strong
                                            style="
                                        color:#C9A227;
                                        font-size:15px;
                                    ">
                                            {{ $setting?->website_name ?? 'Delamoure' }}
                                        </strong>

                                        @if (!empty($setting?->address))
                                            <div style="margin-top:8px;">
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


                            {{-- SOCIAL --}}
                            <div
                                style="
                            margin-top:25px;
                            padding-top:20px;
                            border-top:1px solid rgba(255,255,255,.15);
                        ">

                                @if (!empty($setting?->instagram))
                                    <a href="{{ $setting->instagram }}"
                                        style="
                                        color:#C9A227;
                                        margin-right:15px;
                                        text-decoration:none;
                                        font-size:13px;
                                    ">
                                        Instagram
                                    </a>
                                @endif

                                @if (!empty($setting?->facebook))
                                    <a href="{{ $setting->facebook }}"
                                        style="
                                        color:#C9A227;
                                        margin-right:15px;
                                        text-decoration:none;
                                        font-size:13px;
                                    ">
                                        Facebook
                                    </a>
                                @endif

                                @if (!empty($setting?->tiktok))
                                    <a href="{{ $setting->tiktok }}"
                                        style="
                                        color:#C9A227;
                                        margin-right:15px;
                                        text-decoration:none;
                                        font-size:13px;
                                    ">
                                        TikTok
                                    </a>
                                @endif

                            </div>


                            <p
                                style="
                            margin:25px 0 0;
                            font-size:11px;
                            line-height:1.6;
                            color:#bfaea4;
                        ">
                                © {{ date('Y') }}
                                {{ $setting?->website_name ?? 'Delamoure' }}.
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
