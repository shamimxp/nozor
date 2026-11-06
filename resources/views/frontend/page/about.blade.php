@extends('frontend.layouts.app')

@section('content')
@php
    $phone1 = $settings->contact_number_1 ?? '01674-088383';
    $email = $settings->email ?? 'info@woodmachinery.com.bd';
    $address = $settings->address ?? 'Dhaka, Bangladesh';
    $rawSlogan = trim((string) ($settings->slogan ?? ''));
    $sloganLooksPlaceholder = $rawSlogan === '' || preg_match('/lorem|ipsum|consequatur|quia vel/i', $rawSlogan);
    $slogan = $sloganLooksPlaceholder
        ? 'Industrial woodworking tools, CNC bits & machinery solutions for workshops across Bangladesh.'
        : $rawSlogan;
@endphp

<div class="wm-page">
    <section class="wm-page-hero" aria-label="About WOOD Machinery">
        <div class="wm-page-hero__media" aria-hidden="true">
            <img
                src="{{ asset('web/assets/imgs/banner/wm-promo-power-tools.jpg') }}"
                alt=""
            >
        </div>
        <div class="wm-page-hero__shade" aria-hidden="true"></div>
        <div class="wm-page-hero__grain" aria-hidden="true"></div>

        <div class="container wm-page-hero__inner">
            <h1 class="wm-page-hero__title">Tools built for real workshop work</h1>
            <p class="wm-page-hero__lead">{{ $slogan }}</p>
            <div class="wm-page-hero__actions">
                <a href="{{ route('shop') }}" class="wm-btn wm-btn--brand">Browse products</a>
                <a href="{{ route('contact') }}" class="wm-btn wm-btn--ghost">Talk to us</a>
            </div>
        </div>
    </section>

    <section class="wm-section">
        <div class="container">
            <div class="wm-about-story__grid">
                <div>
                    <span class="wm-kicker">Who we are</span>
                    <h2 class="wm-heading">A machinery partner for Bangladesh workshops</h2>
                    <p class="wm-copy">
                        WOOD Machinery supplies production-ready woodworking tools and spare parts—
                        from CNC bits and router cutters to circular blades and workshop consumables—
                        so factories, furniture makers, and dealers can keep machines running without delay.
                    </p>

                    <div class="wm-about-story__points">
                        <div class="wm-about-story__point">
                            <div class="wm-about-story__point-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V7l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h3>Factory-grade assortment</h3>
                                <p>CNC bits, router bits, blades, bearings, and machine spares selected for daily production use.</p>
                            </div>
                        </div>
                        <div class="wm-about-story__point">
                            <div class="wm-about-story__point-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h13l5 5v5H3V7z"/><path d="M16 7v5h5"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/></svg>
                            </div>
                            <div>
                                <h3>Nationwide reach</h3>
                                <p>Reliable delivery to workshops and dealers across Bangladesh, with clear pricing and support.</p>
                            </div>
                        </div>
                        <div class="wm-about-story__point">
                            <div class="wm-about-story__point-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21a9 9 0 100-18 9 9 0 000 18z"/><path d="M12 7v5l3 2"/></svg>
                            </div>
                            <div>
                                <h3>Practical technical help</h3>
                                <p>Guidance on tool selection and setup so your team spends less time guessing and more time cutting.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wm-about-story__visual">
                    <span class="wm-about-story__accent" aria-hidden="true"></span>
                    <img
                        src="{{ asset('web/assets/imgs/banner/wm-bestsells-banner.jpg') }}"
                        alt="WOOD Machinery woodworking tools and cutters"
                    >
                </div>
            </div>
        </div>
    </section>

    <section class="wm-section wm-section--soft">
        <div class="container">
            <div class="wm-supply__intro">
                <span class="wm-kicker">What we supply</span>
                <h2 class="wm-heading">Core tools for cutting, routing &amp; finishing</h2>
                <p class="wm-copy">Explore the categories workshops order from us most often.</p>
            </div>

            <div class="wm-supply__grid">
                <a href="{{ route('shop') }}" class="wm-supply__item">
                    <img src="{{ asset('web/assets/imgs/banner/wm-promo-cnc-bits.jpg') }}" alt="CNC bits">
                    <div class="wm-supply__label">CNC Bits<small>Production tooling</small></div>
                </a>
                <a href="{{ route('shop') }}" class="wm-supply__item">
                    <img src="{{ asset('web/assets/imgs/banner/wm-promo-router-bits.jpg') }}" alt="Router bits">
                    <div class="wm-supply__label">Router Bits<small>Profiles &amp; cutters</small></div>
                </a>
                <a href="{{ route('shop') }}" class="wm-supply__item">
                    <img src="{{ asset('web/assets/imgs/banner/wm-promo-circular-blades.jpg') }}" alt="Circular blades">
                    <div class="wm-supply__label">Circular Blades<small>Clean, accurate cuts</small></div>
                </a>
                <a href="{{ route('shop') }}" class="wm-supply__item">
                    <img src="{{ asset('web/assets/imgs/banner/wm-promo-power-tools.jpg') }}" alt="Workshop tools">
                    <div class="wm-supply__label">Workshop Tools<small>Machines &amp; spares</small></div>
                </a>
            </div>
        </div>
    </section>

    <section class="wm-section wm-section--navy">
        <div class="container">
            <div class="wm-why__grid">
                <div>
                    <span class="wm-kicker">Why WOOD Machinery</span>
                    <h2 class="wm-heading">Built around workshop uptime</h2>
                    <p class="wm-copy">
                        We focus on the parts and tools that keep production moving—honest specs,
                        dependable stock, and people who understand machinery.
                    </p>
                </div>
                <div>
                    <ul class="wm-why__list">
                        <li>
                            <h3>Genuine product focus</h3>
                            <p>Assortment centered on woodworking machinery needs—not generic marketplace clutter.</p>
                        </li>
                        <li>
                            <h3>Dealer &amp; workshop friendly</h3>
                            <p>Support for retailers, special dealers, and end workshops with clear commercial options.</p>
                        </li>
                        <li>
                            <h3>After-sales care</h3>
                            <p>Help with replacements, spare parts, and practical follow-up when something needs fixing.</p>
                        </li>
                    </ul>

                    <div class="wm-page-cta">
                        <p>Need a quotation, spare part, or tool recommendation? Reach us at {{ $phone1 }} · {{ $email }} · {{ $address }}</p>
                        <a href="{{ route('contact') }}" class="wm-btn wm-btn--brand">Contact WOOD Machinery</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
