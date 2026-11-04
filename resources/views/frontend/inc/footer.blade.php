@php
    $phone1 = $settings->contact_number_1 ?? '01674-088383';
    $phone2 = $settings->contact_number_2 ?? '';
    $email = $settings->email ?? 'info@woodmachinery.com.bd';
    $address = $settings->address ?? 'Dhaka, Bangladesh';
    $hours = $settings->office_hour ?? '10:00 am - 08:00 pm';
    $rawSlogan = trim((string) ($settings->slogan ?? ''));
    $sloganLooksPlaceholder = $rawSlogan === '' || preg_match('/lorem|ipsum|consequatur|quia vel/i', $rawSlogan);
    $slogan = $sloganLooksPlaceholder
        ? 'Industrial woodworking tools, CNC bits & machinery solutions for workshops across Bangladesh.'
        : $rawSlogan;

    // Prefer real categories when they look machinery-related; otherwise use curated business links.
    $machineryHints = '/bit|blade|cnc|router|cutter|bearing|sanding|jali|machine|tool|planer|trimmer|spindle|finger|polo|wood/i';
    $footerCategories = collect();
    if (isset($categories) && $categories->count()) {
        $footerCategories = $categories->filter(function ($cat) use ($machineryHints) {
            return preg_match($machineryHints, (string) $cat->name);
        })->take(7)->values();
    }
    $popularFallback = [
        'Router Bits',
        'CNC Bits',
        'Circular Blades',
        'Sanding Blocks',
        'Bearings & Spares',
        'Jali Machine Blades',
        'All Products',
    ];
@endphp

{{-- Trust / value strip --}}
<section class="wm-trust">
    <div class="container">
        <div class="wm-trust__inner">
            <article class="wm-trust__card">
                <div class="wm-trust__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V7l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
                </div>
                <div>
                    <h3 class="wm-trust__title">Factory-grade quality</h3>
                    <p class="wm-trust__text">CNC bits, blades &amp; cutters built for production</p>
                </div>
            </article>

            <article class="wm-trust__card">
                <div class="wm-trust__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h13l5 5v5H3V7z"/><path d="M16 7v5h5"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/></svg>
                </div>
                <div>
                    <h3 class="wm-trust__title">Nationwide delivery</h3>
                    <p class="wm-trust__text">Fast shipping to workshops across Bangladesh</p>
                </div>
            </article>

            <article class="wm-trust__card">
                <div class="wm-trust__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21a9 9 0 100-18 9 9 0 000 18z"/><path d="M12 7v5l3 2"/></svg>
                </div>
                <div>
                    <h3 class="wm-trust__title">Expert support</h3>
                    <p class="wm-trust__text">Technical help for tools &amp; machine setup</p>
                </div>
            </article>

            <article class="wm-trust__card">
                <div class="wm-trust__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                </div>
                <div>
                    <h3 class="wm-trust__title">Genuine assortment</h3>
                    <p class="wm-trust__text">Router bits, CNC tools, blades &amp; spare parts</p>
                </div>
            </article>

            <article class="wm-trust__card">
                <div class="wm-trust__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 14l4-4 4 4 8-8"/><path d="M14 6h6v6"/></svg>
                </div>
                <div>
                    <h3 class="wm-trust__title">After-sales care</h3>
                    <p class="wm-trust__text">Reliable service &amp; parts for long-term use</p>
                </div>
            </article>
        </div>
    </div>
</section>

{{-- Newsletter --}}
<section class="newsletter wm-newsletter wow animate__animated animate__fadeIn">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="position-relative newsletter-inner wm-newsletter__inner">
                    <div class="newsletter-content">
                        <h2 class="mb-20">
                            Get workshop deals &amp;<br />
                            new machinery updates
                        </h2>
                        <p class="mb-45">Join dealers &amp; workshops sourcing from <span class="text-brand">WOOD Machinery</span></p>
                        <form class="form-subcriber d-flex" id="newsletter-form" action="{{ route('subscribe.store') }}" method="POST">
                            @csrf
                            <input type="email" name="email" id="newsletter-email" placeholder="Your email address" required />
                            <button class="btn" type="submit">Subscribe</button>
                        </form>
                    </div>
                    <img
                        class="wm-newsletter__ad"
                        src="{{ asset('web/assets/imgs/banner/wm-newsletter-ad.png') }}?v={{ filemtime(public_path('web/assets/imgs/banner/wm-newsletter-ad.png')) }}"
                        alt="WOOD Machinery CNC bits, circular blades and workshop tools"
                    >
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<section class="wm-footer">
    <div class="container">
        <div class="wm-footer__grid">
            <div class="wm-footer__brand">
                <a href="{{ url('/') }}" class="wm-footer__logo">
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="WOOD Machinery">
                </a>
                <p class="wm-footer__slogan">{{ $slogan }}</p>
                <ul class="wm-footer__contact">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <span><strong>Address:</strong> {{ $address }}</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.1a2 2 0 012.1-.4c.8.3 1.7.5 2.6.6a2 2 0 011.7 1.9z"/></svg>
                        <span>
                            <strong>Call:</strong>
                            <a href="tel:{{ preg_replace('/\s+/', '', $phone1) }}">{{ $phone1 }}</a>
                            @if($phone2)
                                , <a href="tel:{{ preg_replace('/\s+/', '', $phone2) }}">{{ $phone2 }}</a>
                            @endif
                        </span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
                        <span><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        <span><strong>Hours:</strong> {{ $hours }}</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="wm-footer__title">Company</h4>
                <ul class="wm-footer__list">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('shop') }}">Shop Machinery</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('dealer.login') }}">Dealer Portal</a></li>
                    <li><a href="{{ route('mypanel.login') }}">Worker Panel</a></li>
                    <li><a href="{{ route('contact') }}">Support Center</a></li>
                </ul>
            </div>

            <div>
                <h4 class="wm-footer__title">Account</h4>
                <ul class="wm-footer__list">
                    <li><a href="{{ route('login') }}">Sign In</a></li>
                    <li><a href="{{ route('cart') }}">View Cart</a></li>
                    <li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
                    @auth
                        <li><a href="{{ route('my-account') }}">My Account</a></li>
                        <li><a href="{{ route('my-account') }}">Track My Order</a></li>
                    @else
                        <li><a href="{{ route('login') }}">My Account</a></li>
                        <li><a href="{{ route('login') }}">Track My Order</a></li>
                    @endauth
                    <li><a href="{{ route('contact') }}">Help &amp; Support</a></li>
                </ul>
            </div>

            <div>
                <h4 class="wm-footer__title">Popular</h4>
                <ul class="wm-footer__list">
                    @if($footerCategories->isNotEmpty())
                        @foreach($footerCategories as $cat)
                            <li>
                                <a href="{{ route('category.products', $cat->slug) }}">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    @else
                        @foreach($popularFallback as $label)
                            <li>
                                <a href="{{ route('shop') }}">{{ $label }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <div class="wm-footer__aside">
                <div class="wm-footer__cta">
                    <h4>Need machine advice?</h4>
                    <p>Talk to our team for the right bit, blade or cutter for your workshop.</p>
                    <a class="wm-footer__cta-phone" href="tel:{{ preg_replace('/\s+/', '', $phone1) }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.1a2 2 0 012.1-.4c.8.3 1.7.5 2.6.6a2 2 0 011.7 1.9z"/></svg>
                        {{ $phone1 }}
                    </a>
                    <div>
                        <a class="wm-footer__cta-link" href="{{ route('contact') }}">Visit contact page →</a>
                    </div>
                </div>

                <div class="wm-footer__pay">
                    <div class="wm-footer__pay-head">
                        <h5>Payment Partners</h5>
                        <span>Secure checkout</span>
                    </div>
                    <div class="wm-footer__pay-box">
                        <img src="{{ asset('web/assets/imgs/theme/payment-method.png') }}" alt="Visa, Mastercard, Maestro, American Express">
                    </div>
                </div>
            </div>
        </div>

        <div class="wm-footer__bottom">
            <div class="wm-footer__bottom-row">
                <p class="wm-footer__copy">
                    &copy; {{ date('Y') }}, <strong>WOOD Machinery</strong><br>
                    All rights reserved
                </p>

                <div class="wm-footer__pay-inline">
                    <span>We accept</span>
                    <img src="{{ asset('web/assets/imgs/theme/payment-method.png') }}" alt="Payment partners">
                </div>

                <div class="wm-footer__social">
                    <h6>Follow Us</h6>
                    <div class="wm-footer__social-links">
                        <a href="#" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H9v3h2v7h3v-7h2.5l.5-3H14V9z"/></svg>
                        </a>
                        <a href="#" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12.2s0-3.2-.4-4.7c-.2-.9-.9-1.6-1.8-1.8C19.4 5.3 12 5.3 12 5.3s-7.4 0-8.8.4c-.9.2-1.6.9-1.8 1.8C1 9 1 12.2 1 12.2s0 3.2.4 4.7c.2.9.9 1.6 1.8 1.8 1.4.4 8.8.4 8.8.4s7.4 0 8.8-.4c.9-.2 1.6-.9 1.8-1.8.4-1.5.4-4.7.4-4.7zM9.8 15.5v-6.6l6.2 3.3-6.2 3.3z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6.5-.9a1.1 1.1 0 11-2.2 0 1.1 1.1 0 012.2 0zM12 9a3 3 0 110 6 3 3 0 010-6z"/></svg>
                        </a>
                    </div>
                    <p>Workshop tools for serious production</p>
                </div>
            </div>
        </div>
    </div>
</section>
