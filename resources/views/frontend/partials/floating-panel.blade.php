<div class="cc-panel">
    <div class="cc-header">
        <img src="{{ asset($setting->support_image ?? 'assets/images/whatsapp/profile_01.jpg') }}"
             alt="Support profile image">

        <h2>{{ $setting->support_name ?? 'Springcrest Support' }}</h2>

        <p>{{ $setting->support_role ?? 'Customer Support' }}</p>
    </div>

    <div class="cc-body">
        <p><b>{{ $setting->support_message_title ?? 'Hey there 😊' }}</b></p>

        <p>
            {{ $setting->support_message_body ?? 'Need help? Just give us a call.' }}
        </p>
    </div>

    <div class="cc-footer">
        <a href="tel:{{ $setting->support_phone ?? '+2348000000000' }}"
           class="cc-call-button">
            <span>Call me</span>

            <svg width="13px" height="10px" viewBox="0 0 13 10">
                <path d="M1,5 L11,5"></path>
                <polyline points="8 1 12 5 8 9"></polyline>
            </svg>
        </a>
    </div>
</div>