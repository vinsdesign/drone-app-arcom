<div style="max-width: 100%;" class="h-screen border-t border-gray-200 dark:border-gray-700">
    <div class="px-6 py-12 dark:bg-gray-950 dark:text-white shadow-sm h-screen max-w-4xl mx-auto overflow-y-auto">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <x-filament-panels::logo />
                {{ env('APP_NAME', 'Laravel') }}
            </h1>
            <h2 class="text-lg font-semibold">
                {{ trans('filament-subscriptions::messages.view.billing_management') }}
            </h2>
            <div class="flex items-center mt-6 gap-2">
                <div>{{ trans('filament-subscriptions::messages.view.signed_in_as') }}</div>
                <div>{{ $user->name }}.</div>
            </div>
            <div class="text-sm">
                {{ trans('filament-subscriptions::messages.view.managing_billing_for') }} {{ $user->name }}.
            </div>
            <div class="mt-6">
                {{ trans('filament-subscriptions::messages.view.our_billing_management') }}
            </div>
            <x-filament::link href="{{ url(filament()->getCurrentPanel()->getId()) }}" class="mt-6">
                {{ trans('filament-subscriptions::messages.view.return_to') }}
            </x-filament::link>

            <div class="my-8">
                <a href="{{ url(filament()->getCurrentPanel()->getId()) }}" id="topNavReturnLink" class="lg:hidden flex items-center w-full px-4 py-4 bg-white shadow-lg">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="arrow-left w-4 h-4 text-gray-400">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-2 text-gray-600 underline">
                        {{ trans('filament-subscriptions::messages.view.return_to') }} {{ filament()->getBrandName() }}
                    </div>
                </a>
            </div>

            <div class="px-4 my-4 flex flex-col gap-4">
                <!-- Subscription Plan Sections -->
                <x-filament::section :heading="trans('filament-subscriptions::messages.view.subscribe')">
                    @if (!$user->subscribedPlans()->first())
                        <div class="my-4 bg-gray-200 border border-gray-300 sm:rounded-lg shadow-sm p-6">
                            <p class="text-sm text-gray-600">
                                {{ trans('filament-subscriptions::messages.view.it_looks_like_no_active_subscription') }}
                            </p>
                        </div>
                    @endif

                    <!-- Plans Loop -->
                    <div class="flex flex-col gap-4">
                        @forelse ($plans as $plan)
                            <x-filament::section :heading="$plan->name" :headerActions="[($this->changePlanAction($plan))(['plan' => $plan])]" :description="$plan->description">
                                <div class="my-4">
                                    <span class="text-3xl font-bold">
                                        {{ $plan->isFree() ? trans('filament-subscriptions::messages.view.free') : Number::currency($plan->price + $plan->signup_fee, in: $plan->currency) }}
                                    </span>
                                    @if ($plan->hasTrial())
                                        <span class="text-gray-400">{{ $plan->trial_period }} {{ $plan->trial_interval }} {{ trans('filament-subscriptions::messages.view.trial') }}</span>
                                    @endif
                                </div>
                                <div class="mt-6 flex flex-col gap-2">
                                    @foreach ($plan->features as $feature)
                                        <div class="flex items-center gap-2">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 {{ is_numeric($feature->value) || $feature->value == 'true' || $feature->value == 'unlimited' ? 'text-custom-500' : 'text-gray-400' }}">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-sm text-gray-600">
                                                {{ $feature->name }} {{ is_numeric($feature->value) || $feature->value == 'unlimited' ? '(' . Str::title($feature->value) . ')' : '' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </x-filament::section>
                        @empty
                            <p class="text-sm text-gray-600">{{ trans('filament-subscriptions::messages.view.no_plans_available') }}</p>
                        @endforelse
                    </div>
                </x-filament::section>

                <!-- Cancel Subscription Section -->
                @if ($currentSubscription && $currentSubscription->active())
                    <x-filament::section :heading="trans('filament-subscriptions::messages.view.cancel_subscription')">
                        <p class="text-sm text-gray-600">{{ trans('filament-subscriptions::messages.view.cancel_subscription_info') }}</p>
                        <div class="mt-3">{{ $this->cancelPlanAction }}</div>
                    </x-filament::section>
                @endif
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
