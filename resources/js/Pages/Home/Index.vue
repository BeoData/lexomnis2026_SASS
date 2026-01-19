<template>
    <PublicLayout>
        <!-- Hero Section -->
        <section class="bg-gradient-to-b from-blue-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                        Legal practice management<br />that gets it right
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto mb-8">
                        Streamline your law firm operations with time tracking, billing, document management, and client communication—all in one powerful platform.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                        <Link
                            :href="route('tenant.register') + '?cta=signup-header'"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-4 rounded-lg text-lg transition-colors shadow-lg hover:shadow-xl"
                        >
                            Start Free Trial
                        </Link>
                        <Link
                            :href="route('pricing')"
                            class="bg-white hover:bg-gray-50 text-gray-900 font-semibold px-8 py-4 rounded-lg text-lg border-2 border-gray-300 transition-colors shadow-md hover:shadow-lg"
                        >
                            View Pricing
                        </Link>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>7-day free trial</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Secure & compliant</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span>Rated 4.7/5 by legal professionals</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Everything you need to run your practice
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Powerful features designed specifically for law firms
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1: Time Tracking -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Time Tracking</h3>
                        <p class="text-gray-600">
                            Track billable hours accurately with automatic timers and detailed time entries.
                        </p>
                    </div>

                    <!-- Feature 2: Billing -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Billing & Invoicing</h3>
                        <p class="text-gray-600">
                            Generate professional invoices, accept payments, and manage your billing cycle seamlessly.
                        </p>
                    </div>

                    <!-- Feature 3: Document Management -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Document Management</h3>
                        <p class="text-gray-600">
                            Organize, store, and access all your legal documents in one secure location.
                        </p>
                    </div>

                    <!-- Feature 4: Client Portal -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Client Portal</h3>
                        <p class="text-gray-600">
                            Give your clients secure access to their matters, documents, and billing information.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Trusted by legal professionals
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">100+</div>
                        <p class="text-gray-600">Bar associations approved</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">10,000+</div>
                        <p class="text-gray-600">Legal professionals</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">4.7/5</div>
                        <p class="text-gray-600">Average rating</p>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-wrap justify-center items-center gap-8 pt-8 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-700 mb-1">SOC 2 Certified</div>
                        <p class="text-xs text-gray-500">Security & Compliance</p>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-700 mb-1">ISO 27001</div>
                        <p class="text-xs text-gray-500">Information Security</p>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-700 mb-1">HIPAA Compliant</div>
                        <p class="text-xs text-gray-500">Data Protection</p>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-700 mb-1">24/5 Support</div>
                        <p class="text-xs text-gray-500">Always Available</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="py-20 bg-blue-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Ready to transform your practice?
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    Start your free trial today. No credit card required.
                </p>
                <Link
                    :href="route('tenant.register') + '?cta=signup-footer'"
                    class="inline-block bg-white hover:bg-gray-100 text-blue-600 font-semibold px-8 py-4 rounded-lg text-lg transition-colors shadow-lg hover:shadow-xl"
                >
                    Start Free Trial
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Pages/Layouts/PublicLayout.vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();
</script>
