@extends('emails.layouts.app')

@section('title', 'New Contact Message')

@section('eyebrow', 'Customer Enquiry')

@section('heading')
    New Contact Message
@endsection

@section('subheading')
    A customer has sent a new message through the Delamoure website.
@endsection

@section('content')

    <p style="
        margin:0 0 25px;
        font-size:16px;
        line-height:1.7;
    ">
        Hello,
    </p>

    <p style="
        margin:0 0 30px;
        font-size:15px;
        line-height:1.8;
        color:#555555;
    ">
        A new customer enquiry has been submitted through the website.
        The details are provided below.
    </p>


    <table width="100%" cellpadding="0" cellspacing="0"
        style="
            border-collapse:collapse;
            background:#F8F4EC;
            border-radius:8px;
        ">

        <tr>
            <td
                style="
                padding:15px 20px;
                width:120px;
                border-bottom:1px solid #eadfce;
                font-weight:700;
                color:#4A2C20;
            ">
                Name
            </td>

            <td style="
                padding:15px 20px;
                border-bottom:1px solid #eadfce;
            ">
                {{ $name }}
            </td>
        </tr>

        <tr>
            <td
                style="
                padding:15px 20px;
                border-bottom:1px solid #eadfce;
                font-weight:700;
                color:#4A2C20;
            ">
                Email
            </td>

            <td style="
                padding:15px 20px;
                border-bottom:1px solid #eadfce;
            ">
                <a href="mailto:{{ $email }}" style="color:#6B3F2A;">
                    {{ $email }}
                </a>
            </td>
        </tr>

        <tr>
            <td
                style="
                padding:15px 20px;
                font-weight:700;
                color:#4A2C20;
            ">
                Subject
            </td>

            <td style="padding:15px 20px;">
                {{ $subjectText }}
            </td>
        </tr>

    </table>


    <div style="margin-top:30px;">

        <div
            style="
            font-size:14px;
            font-weight:700;
            color:#4A2C20;
            margin-bottom:10px;
        ">
            Message
        </div>

        <div
            style="
            background:#ffffff;
            border-left:4px solid #C9A227;
            padding:20px;
            line-height:1.8;
            color:#555555;
        ">
            {!! nl2br(e($contactMessage)) !!}
        </div>

    </div>


    <div style="margin-top:35px;">

        <a href="mailto:{{ $email }}"
            style="
                display:inline-block;
                background:#C9A227;
                color:#4A2C20;
                text-decoration:none;
                font-weight:700;
                padding:14px 28px;
                border-radius:30px;
                font-size:14px;
            ">
            Reply to Customer
        </a>

    </div>

@endsection
