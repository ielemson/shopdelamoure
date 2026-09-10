<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $setting?->website_name ?? 'Delamoure')</title>
    <!--[if mso]>
    <style>
        table { border-collapse: collapse; }
        * { font-family: Georgia, 'Times New Roman', serif !important; }
    </style>
    <![endif]-->
    <style>
        @media only screen and (max-width: 650px) {
            .container {
                width: 100% !important;
            }

            .px-outer {
                padding-left: 22px !important;
                padding-right: 22px !important;
            }

            .hero-pad {
                padding: 36px 24px !important;
            }

            .content-pad {
                padding: 38px 24px 28px !important;
            }

            .heading {
                font-size: 24px !important;
            }

            .feature-col {
                display: block !important;
                width: 100% !important;
                padding: 14px 0 !important;
                border-left: none !important;
                border-top: 1px solid #E4D9C8;
            }

            .feature-col:first-child {
                border-top: none;
            }
        }
    </style>
</head>

<body
    style="
    margin:0;
    padding:0;
    background:#EFE9DE;
    font-family:Arial, Helvetica, sans-serif;
    color:#2B2420;
">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#EFE9DE; padding:40px 10px;">
        <tr>
            <td align="center">

                <table class="container" width="620" cellpadding="0" cellspacing="0" border="0"
                    style="
                    width:100%;
                    max-width:620px;
                    background:#FFFFFF;
                ">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center" class="px-outer"
                            style="
                            background:#241A14;
                            padding:30px 30px 26px;
                        ">

                            @if (!empty($setting?->logo))
                                <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $setting->website_name }}"
                                    style="
                                    max-width:150px;
                                    max-height:52px;
                                    display:block;
                                ">
                            @else
                                <div
                                    style="
                                    font-family:Georgia, 'Times New Roman', serif;
                                    font-size:24px;
                                    font-weight:400;
                                    letter-spacing:0.5px;
                                    color:#F5EFE4;
                                ">
                                    {{ $setting?->website_name ?? 'Delamoure' }}
                                </div>
                            @endif

                        </td>
                    </tr>

                    {{-- HAIRLINE --}}
                    <tr>
                        <td style="height:1px; background:#A9825A; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- HERO --}}
                    <tr>
                        <td class="hero-pad"
                            style="
                            background:#2E2118;
                            padding:52px 50px;
                            text-align:center;
                        ">

                            <div
                                style="
                                font-family:Georgia, 'Times New Roman', serif;
                                font-style:italic;
                                color:#C9AE83;
                                font-size:14px;
                                margin-bottom:14px;
                            ">
                                @yield('eyebrow', 'Delamoure')
                            </div>

                            <h1 class="heading"
                                style="
                                margin:0;
                                font-family:Georgia, 'Times New Roman', serif;
                                color:#FAF7F2;
                                font-size:28px;
                                line-height:1.4;
                                font-weight:400;
                            ">
                                @yield('heading')
                            </h1>

                            @hasSection('subheading')
                                <p
                                    style="
                                    margin:16px auto 0;
                                    max-width:440px;
                                    color:#D8CBBB;
                                    font-size:15px;
                                    line-height:1.75;
                                ">
                                    @yield('subheading')
                                </p>
                            @endif

                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td class="content-pad"
                            style="padding:44px 50px 32px; font-size:15px; line-height:1.7; color:#2B2420;">

                            @yield('content')

                        </td>
                    </tr>

                    {{-- CUSTOMER PROMISE --}}
                    <tr>
                        <td class="px-outer"
                            style="
                            padding:8px 50px 40px;
                        ">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>

                                    <td class="feature-col" width="33.33%" align="center" valign="top"
                                        style="padding:0 12px; border-left:1px solid #E4D9C8;">
                                        <div
                                            style="font-family:Georgia, 'Times New Roman', serif; font-style:italic; font-size:13.5px; color:#241A14;">
                                            Quality assured
                                        </div>
                                    </td>

                                    <td class="feature-col" width="33.33%" align="center" valign="top"
                                        style="padding:0 12px; border-left:1px solid #E4D9C8;">
                                        <div
                                            style="font-family:Georgia, 'Times New Roman', serif; font-style:italic; font-size:13.5px; color:#241A14;">
                                            Secure shopping
                                        </div>
                                    </td>

                                    <td class="feature-col" width="33.33%" align="center" valign="top"
                                        style="padding:0 12px; border-left:1px solid #E4D9C8;">
                                        <div
                                            style="font-family:Georgia, 'Times New Roman', serif; font-style:italic; font-size:13.5px; color:#241A14;">
                                            Attentive care
                                        </div>
                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- FOOTER LINKS --}}
                    <tr>
                        <td align="center"
                            style="background:#241A14; padding:22px; border-top:1px solid rgba(255,255,255,.08);">

                            <a href="{{ url('/') }}"
                                style="color:#D8CBBB; text-decoration:none; font-size:12.5px; margin:0 14px;">
                                Shop
                            </a>

                            <a href="{{ url('/about-us') }}"
                                style="color:#D8CBBB; text-decoration:none; font-size:12.5px; margin:0 14px;">
                                About Us
                            </a>

                            <a href="{{ url('/contact-us') }}"
                                style="color:#D8CBBB; text-decoration:none; font-size:12.5px; margin:0 14px;">
                                Contact
                            </a>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td class="px-outer" style="background:#1B140F; padding:36px 50px; color:#B8A99A;">

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td valign="top" align="center" style="font-size:12.5px; line-height:1.8;">

                                        <div
                                            style="font-family:Georgia, 'Times New Roman', serif; color:#C9AE83; font-size:15px; letter-spacing:0.3px;">
                                            {{ $setting?->website_name ?? 'Delamoure' }}
                                        </div>

                                        @if (!empty($setting?->address))
                                            <div style="margin-top:10px;">
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
                                margin-top:22px;
                                padding-top:18px;
                                border-top:1px solid rgba(255,255,255,.08);
                                text-align:center;
                            ">

                                @if (!empty($setting?->instagram))
                                    <a href="{{ $setting->instagram }}"
                                        style="color:#A9825A; margin:0 10px; text-decoration:none; font-size:12px;">
                                        Instagram
                                    </a>
                                @endif

                                @if (!empty($setting?->facebook))
                                    <a href="{{ $setting->facebook }}"
                                        style="color:#A9825A; margin:0 10px; text-decoration:none; font-size:12px;">
                                        Facebook
                                    </a>
                                @endif

                                @if (!empty($setting?->tiktok))
                                    <a href="{{ $setting->tiktok }}"
                                        style="color:#A9825A; margin:0 10px; text-decoration:none; font-size:12px;">
                                        TikTok
                                    </a>
                                @endif

                            </div>

                            <p
                                style="
                                margin:22px 0 0;
                                font-size:11px;
                                line-height:1.6;
                                color:#7A6D62;
                                text-align:center;
                            ">
                                © {{ date('Y') }} {{ $setting?->website_name ?? 'Delamoure' }}. All rights
                                reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
