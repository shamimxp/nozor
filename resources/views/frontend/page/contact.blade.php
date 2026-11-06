@extends('frontend.layouts.app')

@section('content')
@php
    $phone1 = $settings->contact_number_1 ?? '01674-088383';
    $phone2 = $settings->contact_number_2 ?? '';
    $email = $settings->email ?? 'info@woodmachinery.com.bd';
    $address = $settings->address ?? 'Dhaka, Bangladesh';
    $hours = $settings->office_hour ?? '10:00 am - 08:00 pm';
    $whatsapp = $settings->whatsapp ?? '';
    $phoneTel = preg_replace('/\s+/', '', $phone1);
    $phone2Tel = $phone2 ? preg_replace('/\s+/', '', $phone2) : '';
    $mapQuery = urlencode($address);
    $whatsappLink = $whatsapp !== ''
        ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp)
        : null;
@endphp

<div class="wm-page">
    <section class="wm-page-hero" aria-label="Contact WOOD Machinery">
        <div class="wm-page-hero__media" aria-hidden="true">
            <img
                src="{{ asset('web/assets/imgs/banner/wm-promo-cnc-bits.jpg') }}"
                alt=""
            >
        </div>
        <div class="wm-page-hero__shade" aria-hidden="true"></div>
        <div class="wm-page-hero__grain" aria-hidden="true"></div>

        <div class="container wm-page-hero__inner">
            <h1 class="wm-page-hero__title">Get in touch with our team</h1>
            <p class="wm-page-hero__lead">
                Ask about tools, spare parts, dealer pricing, or delivery across Bangladesh.
                We respond during office hours and help you find the right machinery solution.
            </p>
            <div class="wm-page-hero__actions">
                <a href="tel:{{ $phoneTel }}" class="wm-btn wm-btn--brand">Call {{ $phone1 }}</a>
                <a href="#contact-form" class="wm-btn wm-btn--ghost">Send a message</a>
            </div>
        </div>
    </section>

    <section class="wm-contact">
        <div class="container">
            <div class="wm-contact__grid">
                <aside>
                    <span class="wm-kicker">Contact details</span>
                    <h2 class="wm-heading">Visit, call, or write</h2>
                    <p class="wm-copy" style="margin-bottom: 1.5rem;">
                        Use the details below or send a message—we’ll get back with product guidance and pricing.
                    </p>

                    <div class="wm-contact__details">
                        <div class="wm-contact__item">
                            <div class="wm-contact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            </div>
                            <div>
                                <h3>Address</h3>
                                <p>{{ $address }}</p>
                            </div>
                        </div>

                        <div class="wm-contact__item">
                            <div class="wm-contact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.1a2 2 0 012.1-.4c.8.3 1.7.5 2.6.6a2 2 0 011.7 1.9z"/></svg>
                            </div>
                            <div>
                                <h3>Phone</h3>
                                <p>
                                    <a href="tel:{{ $phoneTel }}">{{ $phone1 }}</a>
                                    @if($phone2)
                                        <br><a href="tel:{{ $phone2Tel }}">{{ $phone2 }}</a>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="wm-contact__item">
                            <div class="wm-contact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M4 7l8 6 8-6"/></svg>
                            </div>
                            <div>
                                <h3>Email</h3>
                                <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
                            </div>
                        </div>

                        <div class="wm-contact__item">
                            <div class="wm-contact__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </div>
                            <div>
                                <h3>Office hours</h3>
                                <p>{{ $hours }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="wm-contact__actions">
                        <a
                            class="wm-btn wm-btn--outline"
                            href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            View on map
                        </a>
                        @if($whatsappLink)
                            <a class="wm-btn wm-btn--brand" href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer">
                                WhatsApp
                            </a>
                        @endif
                    </div>
                </aside>

                <div class="wm-contact__form-wrap" id="contact-form">
                    <span class="wm-kicker">Message us</span>
                    <h2 class="wm-heading">Send an inquiry</h2>
                    <p class="wm-copy">Tell us what you need—tool type, quantity, or machine model—and we’ll reply shortly.</p>

                    @if(session('success'))
                        <div class="wm-alert wm-alert--success" role="status">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="wm-alert" role="alert">
                            Please check the highlighted fields and try again.
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="post" novalidate>
                        @csrf
                        <div class="wm-form-row">
                            <div class="wm-field {{ $errors->has('first_name') ? 'is-invalid' : '' }}">
                                <label for="first_name">Full name *</label>
                                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="name">
                                @error('first_name')<span class="wm-field__error">{{ $message }}</span>@enderror
                            </div>
                            <div class="wm-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
                                <label for="email">Email *</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')<span class="wm-field__error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="wm-form-row">
                            <div class="wm-field {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                                <label for="phone">Phone *</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autocomplete="tel">
                                @error('phone')<span class="wm-field__error">{{ $message }}</span>@enderror
                            </div>
                            <div class="wm-field {{ $errors->has('subject') ? 'is-invalid' : '' }}">
                                <label for="subject">Subject *</label>
                                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required>
                                @error('subject')<span class="wm-field__error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="wm-field {{ $errors->has('message') ? 'is-invalid' : '' }}">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" required placeholder="Product names, quantities, delivery city…">{{ old('message') }}</textarea>
                            @error('message')<span class="wm-field__error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="wm-btn wm-btn--brand">Send message</button>
                        <p class="wm-form-note">Required fields are marked *. We only use your details to respond to this inquiry.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
