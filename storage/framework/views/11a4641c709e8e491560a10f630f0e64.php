<?php $__env->startSection('title', 'Checkout — TolBazar'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

<style>[x-cloak]{display:none!important}</style>

<?php
    $isDirect    = $isDirect ?? false;
    $firstMeta   = $summary['items'][0]['meta'] ?? [];
    $totalAmount = (float) $summary['total'];
    $minAdvance  = max(100, round($totalAmount * 0.20, 2));
    $maxAdvance  = max($minAdvance, $totalAmount - 0.01);
    $isAuth      = auth()->check();
    $walletBal   = (float) ($walletBalance ?? 0);
    $hasPin      = (bool) ($walletHasPin ?? false);

    $oldMethod  = old('payment_method', 'cod');
    $oldType    = old('payment_type', 'full');
    $oldAdvance = old('advance_amount', $minAdvance);
?>

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Checkout</h1>
            <p class="text-sm text-gray-500 mb-0">Review and confirm your booking details</p>
        </div>

        <?php if(session('error')): ?>
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                <ul class="list-disc pl-4 mb-0 space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($e); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('checkout.store')); ?>" method="POST"
              x-data="checkoutForm()"
              @submit="submitting = true">
            <?php echo csrf_field(); ?>
            
            <input type="hidden" name="_booking_token" value="<?php echo e($bookingToken ?? ''); ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                
                <div class="lg:col-span-2 space-y-4">

                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4 mt-0 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            Customer Information
                        </h2>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name"
                                       value="<?php echo e(old('customer_name', auth()->user()?->name)); ?>" required
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="customer_email"
                                           value="<?php echo e(old('customer_email', auth()->user()?->email)); ?>" required
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" name="customer_phone"
                                           value="<?php echo e(old('customer_phone', auth()->user()?->mobile_number)); ?>" required
                                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <?php if($isDirect && !empty($firstMeta)): ?>
                        <div class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h2 class="text-base font-semibold text-gray-900 mb-4 mt-0 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                Booking Details
                            </h2>

                            <div class="space-y-2 text-sm">
                                <?php if(isset($firstMeta['check_in'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Check-in</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['check_in'])->format('d M Y')); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Check-out</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['check_out'])->format('d M Y')); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Guests</span>
                                        <span class="font-semibold text-gray-900"><?php echo e($firstMeta['guests']); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Rooms</span>
                                        <span class="font-semibold text-gray-900"><?php echo e($firstMeta['rooms']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['appointment_date'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Date</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['appointment_date'])->format('d M Y')); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Time</span>
                                        <span class="font-semibold text-gray-900"><?php echo e($firstMeta['appointment_time']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['batch'])): ?>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Batch</span>
                                        <span class="font-semibold text-gray-900 capitalize"><?php echo e($firstMeta['batch']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['pickup'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Pickup</span>
                                        <span class="font-semibold text-gray-900 text-right"><?php echo e($firstMeta['pickup']); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Dropoff</span>
                                        <span class="font-semibold text-gray-900 text-right"><?php echo e($firstMeta['dropoff']); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Date</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['travel_date'])->format('d M Y')); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Passengers</span>
                                        <span class="font-semibold text-gray-900"><?php echo e($firstMeta['passengers']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['preferred_date'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Preferred Date</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['preferred_date'])->format('d M Y')); ?></span>
                                    </div>
                                    <?php if(!empty($firstMeta['issue'])): ?>
                                        <div class="py-2">
                                            <p class="text-gray-500 mb-1 mt-0">Issue:</p>
                                            <p class="text-gray-800 bg-gray-50 rounded-lg p-2 mb-0"><?php echo e($firstMeta['issue']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['site_address'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Site Address</span>
                                        <span class="font-semibold text-gray-900 text-right"><?php echo e($firstMeta['site_address']); ?></span>
                                    </div>
                                    <?php if(!empty($firstMeta['project_size'])): ?>
                                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                                            <span class="text-gray-500">Project Size</span>
                                            <span class="font-semibold text-gray-900"><?php echo e($firstMeta['project_size']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($firstMeta['requirements'])): ?>
                                        <div class="py-2">
                                            <p class="text-gray-500 mb-1 mt-0">Requirements:</p>
                                            <p class="text-gray-800 bg-gray-50 rounded-lg p-2 mb-0"><?php echo e($firstMeta['requirements']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(isset($firstMeta['departure_date'])): ?>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Departure</span>
                                        <span class="font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($firstMeta['departure_date'])->format('d M Y')); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Travelers</span>
                                        <span class="font-semibold text-gray-900"><?php echo e($firstMeta['travelers']); ?></span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Room Type</span>
                                        <span class="font-semibold text-gray-900 capitalize"><?php echo e($firstMeta['room_type']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    
                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4 mt-0 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </span>
                            Payment Method
                        </h2>

                        
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                   :class="method === 'cod' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-300'">
                                <input type="radio" name="payment_method" value="cod"
                                       x-model="method" class="w-4 h-4 text-blue-600">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0">Cash on Delivery</p>
                                    <p class="text-[11px] text-gray-500 mb-0">Pay on delivery/service</p>
                                </div>
                            </label>

                            
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                   :class="method === 'wallet' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-300'
                                           + (!isAuth ? ' opacity-50 cursor-not-allowed' : '')">
                                <input type="radio" name="payment_method" value="wallet"
                                       x-model="method" :disabled="!isAuth" class="w-4 h-4 text-blue-600">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 mb-0">TolBazar Wallet</p>
                                    <p class="text-[11px] text-gray-500 mb-0">
                                        <?php if($isAuth): ?>
                                            Balance: Rs. <?php echo e(number_format($walletBal, 2)); ?>

                                        <?php else: ?>
                                            <a href="<?php echo e(route('login')); ?>" class="text-blue-600 underline">Login to use</a>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </label>
                        </div>

                        
                        
                        
                        <fieldset x-show="method === 'wallet'" x-cloak
                                  :disabled="method !== 'wallet'"
                                  class="border-0 p-0 m-0 min-w-0 space-y-3">

                            <?php if(!$isAuth): ?>
                                
                                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                                    <p class="font-semibold mb-1 mt-0">Login required</p>
                                    <p class="mb-2">You need to <a href="<?php echo e(route('login')); ?>" class="underline font-semibold">login</a> to pay with wallet.</p>
                                </div>
                            <?php elseif(!$hasPin): ?>
                                
                                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                                    <p class="font-semibold mb-1 mt-0">Wallet PIN not set</p>
                                    <p class="mb-2">Set a 6-digit PIN to protect your wallet before making payments.</p>
                                    <a href="<?php echo e(route('wallet.security')); ?>"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-amber-900 bg-white border border-amber-300 hover:bg-amber-100">
                                        Set PIN Now →
                                    </a>
                                </div>
                            <?php else: ?>
                                
                                <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs uppercase tracking-wide text-blue-700 font-semibold mb-0">Wallet Balance</p>
                                        <a href="<?php echo e(route('wallet.dashboard')); ?>" class="text-[11px] text-blue-600 hover:underline">View Wallet →</a>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-900 mb-0">Rs. <?php echo e(number_format($walletBal, 2)); ?></p>
                                    <?php if($walletBal < $totalAmount): ?>
                                        <p class="text-[11px] text-amber-700 mt-1 mb-0">
                                            ⚠️ Balance is less than total. Use Advance Payment option.
                                        </p>
                                    <?php endif; ?>
                                </div>

                                
                                <div class="space-y-2">
                                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition"
                                           :class="payType === 'full' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-200'
                                                   + (balance < total ? ' opacity-50 cursor-not-allowed' : '')">
                                        <input type="radio" name="payment_type" value="full"
                                               x-model="payType" :disabled="balance < total"
                                               class="mt-0.5 w-4 h-4 text-blue-600">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <p class="text-sm font-semibold text-gray-900 mb-0">Full Payment</p>
                                                <p class="text-sm font-bold text-gray-900 mb-0">Rs. <?php echo e(number_format($totalAmount, 2)); ?></p>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mb-0">Pay full amount now — nothing due later</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition"
                                           :class="payType === 'advance' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-gray-200 hover:border-blue-200'">
                                        <input type="radio" name="payment_type" value="advance"
                                               x-model="payType" class="mt-0.5 w-4 h-4 text-blue-600">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <p class="text-sm font-semibold text-gray-900 mb-0">Advance Payment</p>
                                                <p class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Recommended</p>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mb-0">Pay partial now, rest later</p>
                                        </div>
                                    </label>
                                </div>

                                
                                <div x-show="payType === 'advance'" x-cloak class="space-y-2 pl-7">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                            Advance Amount
                                            <span class="text-gray-400 font-normal">
                                                (min Rs. <?php echo e(number_format($minAdvance, 2)); ?>)
                                            </span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rs.</span>
                                            <input type="number" name="advance_amount"
                                                   x-model.number="advance"
                                                   :disabled="payType !== 'advance'"
                                                   min="<?php echo e($minAdvance); ?>" max="<?php echo e($maxAdvance); ?>" step="0.01"
                                                   class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                                        </div>
                                    </div>
                                    <div class="flex gap-1.5">
                                        <button type="button" @click="setPercent(25)"
                                                class="flex-1 px-2 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">25%</button>
                                        <button type="button" @click="setPercent(50)"
                                                class="flex-1 px-2 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">50%</button>
                                        <button type="button" @click="setPercent(75)"
                                                class="flex-1 px-2 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">75%</button>
                                    </div>
                                </div>

                                
                                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-emerald-800 font-medium">Paying now</span>
                                        <span class="text-emerald-900 font-bold">Rs. <span x-text="paidNow.toFixed(2)"></span></span>
                                    </div>
                                    <div class="flex justify-between text-xs" x-show="remaining > 0" x-cloak>
                                        <span class="text-amber-800 font-medium">Remaining due</span>
                                        <span class="text-amber-900 font-bold">Rs. <span x-text="remaining.toFixed(2)"></span></span>
                                    </div>
                                    <div x-show="paidNow > balance" x-cloak class="text-[11px] text-red-700 font-semibold pt-1 border-t border-emerald-200">
                                        ⚠️ Insufficient balance — please reduce advance amount.
                                    </div>
                                </div>

                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                        Enter Wallet PIN <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" name="wallet_pin"
                                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                                           autocomplete="off"
                                           placeholder="••••••"
                                           :required="method === 'wallet'"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm text-center font-mono tracking-widest focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                                    <p class="text-[11px] text-gray-500 mt-1 mb-0">
                                        <svg class="inline w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        PIN is required to protect your wallet
                                    </p>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                    </div>

                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Order Notes (optional)</label>
                        <textarea name="notes" rows="2" placeholder="Any additional instructions..."
                                  class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm resize-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 sticky top-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4 mt-0">Order Summary</h2>

                        
                        <div class="space-y-3 mb-4 max-h-80 overflow-y-auto">
                            <?php $__currentLoopData = $summary['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex gap-3 items-start text-sm">
                                    <?php if($item['product']->thumbnail): ?>
                                        <img src="<?php echo e(Storage::url($item['product']->thumbnail)); ?>"
                                             class="w-12 h-12 rounded-lg object-cover shrink-0">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center text-gray-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-900 font-medium mb-0 truncate"><?php echo e($item['product']->name); ?></p>
                                        <p class="text-xs text-gray-500 mb-0">Qty: <?php echo e($item['quantity']); ?> × Rs. <?php echo e(number_format($item['price'], 2)); ?></p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 mb-0 shrink-0">
                                        Rs. <?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        
                        <div class="pt-3 border-t border-gray-100 space-y-1.5 text-sm">

                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rs. <?php echo e(number_format($summary['subtotal'], 2)); ?></span>
                            </div>

                            <?php if(!empty($summary['charges_breakdown'])): ?>
                                <?php $__currentLoopData = $summary['charges_breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex justify-between text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <?php echo e($ch['name']); ?>

                                            <?php if(($ch['type'] ?? '') === 'percentage'): ?>
                                                <span class="text-[10px] text-gray-400">(<?php echo e($ch['rate']); ?>%)</span>
                                            <?php endif; ?>
                                            <?php if(!empty($ch['is_taxable'])): ?>
                                                <span class="text-[9px] px-1 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold">+tax</span>
                                            <?php endif; ?>
                                        </span>
                                        <span>Rs. <?php echo e(number_format($ch['amount'], 2)); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            <?php if(!empty($summary['tax_breakdown'])): ?>
                                <?php $__currentLoopData = $summary['tax_breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex justify-between <?php echo e(!empty($tax['is_inclusive']) ? 'text-gray-400' : 'text-gray-600'); ?>">
                                        <span class="flex items-center gap-1">
                                            <?php echo e($tax['name']); ?>

                                            <?php if(($tax['type'] ?? '') === 'percentage'): ?>
                                                <span class="text-[10px] text-gray-400">(<?php echo e($tax['rate']); ?>%)</span>
                                            <?php endif; ?>
                                            <?php if(!empty($tax['is_inclusive'])): ?>
                                                <span class="text-[9px] px-1 py-0.5 rounded bg-gray-100 text-gray-500 font-semibold">included</span>
                                            <?php endif; ?>
                                        </span>
                                        <span>Rs. <?php echo e(number_format($tax['amount'], 2)); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            
                            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100 mt-1">
                                <span>Total</span>
                                <span>Rs. <?php echo e(number_format($summary['total'], 2)); ?></span>
                            </div>

                            
                            <div x-show="method === 'wallet' && paidNow > 0" x-cloak class="pt-3 border-t border-gray-100 mt-2 space-y-1">
                                <div class="flex justify-between text-emerald-700 text-sm font-semibold">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Paying now (Wallet)
                                    </span>
                                    <span>Rs. <span x-text="paidNow.toFixed(2)"></span></span>
                                </div>
                                <div x-show="remaining > 0" x-cloak class="flex justify-between text-amber-700 text-sm font-semibold">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Remaining Due
                                    </span>
                                    <span>Rs. <span x-text="remaining.toFixed(2)"></span></span>
                                </div>
                            </div>

                            
                            <div x-show="method === 'cod'" class="pt-3 border-t border-gray-100 mt-2">
                                <p class="text-xs text-gray-600 bg-gray-50 rounded-lg p-2 mb-0">
                                    💵 Full amount will be collected on delivery/service.
                                </p>
                            </div>
                        </div>

                        
                        <button type="submit"
                                :disabled="!canSubmit || submitting"
                                :class="(canSubmit && !submitting) ? 'bg-emerald-600 hover:bg-emerald-700 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                class="w-full mt-4 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-semibold text-white shadow-sm transition">
                            <template x-if="submitting">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <template x-if="!submitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <span x-text="submitting ? 'Processing…' : (method === 'wallet' && paidNow > 0 ? 'Pay & Confirm' : 'Place Order')">Place Order</span>
                        </button>

                        <p class="text-[11px] text-gray-500 text-center mt-3 mb-0">
                            By placing this order you agree to TolBazar's Terms & Privacy Policy.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function checkoutForm() {
    return {
        method:  <?php echo json_encode($oldMethod, 15, 512) ?>,
        payType: <?php echo json_encode($oldType, 15, 512) ?>,
        advance: <?php echo e((float) $oldAdvance); ?>,
        total:   <?php echo e((float) $totalAmount); ?>,
        minAdvance: <?php echo e((float) $minAdvance); ?>,
        balance: <?php echo e((float) $walletBal); ?>,
        isAuth:  <?php echo e($isAuth ? 'true' : 'false'); ?>,
        hasPin:  <?php echo e($hasPin ? 'true' : 'false'); ?>,
        submitting: false,

        get paidNow() {
            if (this.payType === 'full') return this.total;
            const a = parseFloat(this.advance) || 0;
            return Math.min(a, this.total);
        },
        get remaining() {
            return Math.max(0, this.total - this.paidNow);
        },
        get canSubmit() {
            if (this.method === 'cod') return true;
            if (this.method === 'wallet') {
                if (!this.isAuth || !this.hasPin) return false;
                if (this.paidNow <= 0) return false;
                if (this.paidNow > this.balance) return false;
                if (this.payType === 'advance' && this.paidNow < this.minAdvance) return false;
                return true;
            }
            return false;
        },
        setPercent(pct) {
            const val = Math.round(this.total * pct) / 100;
            this.advance = Math.max(this.minAdvance, Math.min(val, this.total - 0.01));
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/public/checkout.blade.php ENDPATH**/ ?>