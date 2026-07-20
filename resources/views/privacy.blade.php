<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy · Orbit PT</title>
    <meta name="description" content="Orbit PT Privacy Policy – how we collect, use, and protect your data.">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">


    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #7c3aed, #2563eb, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .orbit-ring {
            animation: orbit-spin 18s linear infinite;
        }
        @keyframes orbit-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="antialiased text-gray-900 bg-white">

    {{-- ─── NAV ──────────────────────────────────────────────────── --}}
    <header class="fixed top-0 inset-x-0 z-50 border-b border-gray-200/80 backdrop-blur-md"
        style="background:rgba(255,255,255,0.85);">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}">
                <x-logo />
            </a>
            <a href="{{ route('home') }}"
                class="text-sm text-gray-500 hover:text-gray-900 transition-colors">← Back to home</a>
        </div>
    </header>

    <main class="pt-28 pb-24 px-6">
        <div class="max-w-3xl mx-auto">

            <p class="text-violet-600 text-sm font-medium uppercase tracking-widest mb-3">Legal</p>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-3">Privacy <span class="gradient-text">Policy</span></h1>
            <p class="text-gray-400 text-sm mb-12">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose prose-gray max-w-none space-y-10 text-gray-600 leading-relaxed">

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Who We Are</h2>
                    <p>Orbit PT ("Orbit", "we", "us", or "our") is an AI-powered personal training service delivered
                        via Telegram. Our registered contact email is
                        <a href="mailto:privacy@orbitpersonaltrainer.com" class="text-violet-600 hover:underline">privacy@orbitpersonaltrainer.com</a>.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Information We Collect</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Account data:</strong> Your Telegram user ID, username, and display name when you
                            connect your account.</li>
                        <li><strong>Health &amp; fitness data:</strong> Body metrics (weight, height, age, sex),
                            fitness goals, activity level, dietary preferences, and progress photos you voluntarily
                            share with the bot.</li>
                        <li><strong>Usage data:</strong> Messages sent to the bot, meal photos, workout logs, and
                            in-app interactions.</li>
                        <li><strong>Payment data:</strong> Billing information processed securely by Stripe. We do
                            not store your card details.</li>
                        <li><strong>Technical data:</strong> IP address, browser type, and device information when
                            you visit our website.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">3. How We Use Your Information</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>To generate and personalise your training splits and meal plans.</li>
                        <li>To provide daily coaching, check-ins, and progress tracking.</li>
                        <li>To process payments and manage your subscription.</li>
                        <li>To improve the accuracy and quality of Orbit's AI models.</li>
                        <li>To communicate service updates and support responses.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Data Sharing</h2>
                    <p>We do not sell your personal data. We share data only with:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-3">
                        <li><strong>Stripe</strong> – payment processing.</li>
                        <li><strong>Telegram</strong> – message delivery infrastructure.</li>
                        <li><strong>AI model providers</strong> (e.g., Anthropic, Google) – solely to generate your
                            personalised plans. Data sent is anonymised where possible.</li>
                        <li><strong>Infrastructure providers</strong> – cloud hosting and database services bound by
                            data-processing agreements.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Health Data &amp; Medical Disclaimer</h2>
                    <div class="p-5 rounded-xl border border-amber-300 bg-amber-50 text-amber-900">
                        <p class="font-semibold mb-2">⚠ Important – Not Medical Advice</p>
                        <p>Orbit PT is a fitness and nutrition guidance service only. The training plans, meal
                            suggestions, macro calculations, and all other content provided by Orbit <strong>do not
                            constitute medical advice, diagnosis, or treatment</strong>. Always consult a qualified
                            medical professional or registered dietitian before starting any new exercise or nutrition
                            programme, particularly if you have a pre-existing health condition, injury, or
                            disability.</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Data Retention</h2>
                    <p>We retain your personal data for as long as your account is active. If you cancel and request
                        deletion, we will erase your data within 30 days, except where retention is required by law.
                        Aggregated, anonymised data may be retained indefinitely for product improvement.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Your Rights</h2>
                    <p>Depending on your jurisdiction you may have the right to:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-3">
                        <li>Access, correct, or delete your personal data.</li>
                        <li>Restrict or object to processing.</li>
                        <li>Receive a portable copy of your data.</li>
                        <li>Withdraw consent at any time.</li>
                    </ul>
                    <p class="mt-3">To exercise any of these rights, contact us at
                        <a href="mailto:privacy@orbitpersonaltrainer.com" class="text-violet-600 hover:underline">privacy@orbitpersonaltrainer.com</a>.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Cookies</h2>
                    <p>Our website uses essential cookies for authentication and session management. We do not use
                        third-party advertising cookies.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Changes to This Policy</h2>
                    <p>We may update this policy from time to time. Material changes will be communicated via the bot
                        or email. Continued use of Orbit after changes are published constitutes acceptance.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Contact</h2>
                    <p>Questions? Email us at
                        <a href="mailto:privacy@orbitpersonaltrainer.com" class="text-violet-600 hover:underline">privacy@orbitpersonaltrainer.com</a>.
                    </p>
                </section>

            </div>
        </div>
    </main>

    {{-- ─── FOOTER ────────────────────────────────────────────────── --}}
    <footer class="py-10 px-6 border-t border-gray-200" style="background:#f8f7ff;">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-gray-400 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-violet-500 to-cyan-400"></div>
                <span>Orbit PT &copy; {{ date('Y') }}</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('privacy') }}" class="text-violet-600 font-medium transition-colors">Privacy</a>
                <a href="{{ route('terms') }}" class="hover:text-gray-600 transition-colors">Terms</a>
                <a href="mailto:support@orbitpersonaltrainer.com" class="hover:text-gray-600 transition-colors">Contact</a>
            </div>
        </div>
    </footer>

</body>

</html>
