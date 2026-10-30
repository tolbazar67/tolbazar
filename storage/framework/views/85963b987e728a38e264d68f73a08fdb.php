<?php $__env->startSection('title', 'Your Cart — TolBazar'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-4xl mx-auto px-4">

        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Shopping Cart</h1>
                <p class="text-sm text-gray-500 mb-0">
                    <?php echo e($items->sum('quantity')); ?> <?php echo e(\Illuminate\Support\Str::plural('item', $items->sum('quantity'))); ?> in your cart
                </p>
            </div>
            <a href="<?php echo e(route('services.index')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Continue Shopping
            </a>
        </div>

        
        <?php if(session('success')): ?>
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <?php if($items->isEmpty()): ?>
            
            <div class="bg-white border border-gray-200 rounded-2xl p-16 text-center">
                <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-600 font-semibold mb-1">Your cart is empty</p>
                <p class="text-sm text-gray-500 mb-4">Add products to start shopping</p>
                <a href="<?php echo e(route('services.index')); ?>"
                   class="inline-block px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                
                <div class="lg:col-span-2 space-y-3">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center gap-4">
                            
                            <a href="<?php echo e(route('services.product', [$item->product->service->slug ?? 'product', $item->product->slug])); ?>"
                               class="shrink-0">
                                <?php if($item->product->thumbnail): ?>
                                    <img src="<?php echo e(Storage::url($item->product->thumbnail)); ?>"
                                         alt="<?php echo e($item->product->name); ?>"
                                         class="w-20 h-20 rounded-xl object-cover hover:opacity-80 transition">
                                <?php else: ?>
                                    <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </a>

                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 mb-0 truncate"><?php echo e($item->product->name); ?></p>
                                <p class="text-xs text-gray-500 mb-1"><?php echo e($item->product->branch?->branch_name); ?></p>
                                <p class="text-sm font-semibold text-blue-600 mb-0">Rs. <?php echo e(number_format($item->unit_price, 2)); ?></p>
                                <?php if($item->meta['note'] ?? null): ?>
                                    <p class="text-[11px] text-gray-500 mt-1 mb-0 italic">Note: <?php echo e($item->meta['note']); ?></p>
                                <?php endif; ?>
                            </div>

                            
                            <form action="<?php echo e(route('cart.update', $item)); ?>" method="POST" class="flex items-center">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            onclick="const i=this.nextElementSibling; i.value=Math.max(1,i.value-1); i.form.submit();"
                                            class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-bold transition">−</button>
                                    <input type="number" name="quantity" value="<?php echo e($item->quantity); ?>" min="1" max="99"
                                           onchange="this.form.submit()"
                                           class="w-12 text-center px-1 py-1 rounded-lg border border-gray-300 text-sm font-semibold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                                    <button type="button"
                                            onclick="const i=this.previousElementSibling; i.value=Math.min(99,+i.value+1); i.form.submit();"
                                            class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-bold transition">+</button>
                                </div>
                            </form>

                            
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-gray-900 mb-1">Rs. <?php echo e(number_format($item->line_total, 2)); ?></p>
                                <form action="<?php echo e(route('cart.remove', $item)); ?>" method="POST"
                                      onsubmit="return confirm('Remove this item from cart?');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <form action="<?php echo e(route('cart.clear')); ?>" method="POST" class="text-right"
                          onsubmit="return confirm('Clear entire cart?');">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-xs text-gray-500 hover:text-red-600 font-medium">
                            Clear entire cart
                        </button>
                    </form>
                </div>

                
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 sticky top-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4 mt-0">Order Summary</h2>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rs. <?php echo e(number_format($subtotal, 2)); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax</span>
                                <span>Rs. <?php echo e(number_format($tax, 2)); ?></span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100">
                                <span>Total</span>
                                <span>Rs. <?php echo e(number_format($total, 2)); ?></span>
                            </div>
                        </div>

                        <a href="<?php echo e(route('checkout')); ?>"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Proceed to Checkout
                        </a>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Secure checkout
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/public/cart.blade.php ENDPATH**/ ?>