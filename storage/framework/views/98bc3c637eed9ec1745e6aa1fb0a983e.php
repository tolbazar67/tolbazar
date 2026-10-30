<?php $__env->startSection('title', 'Pending Cash Collection — Seller'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<?php
    $statusColors = [
        'pending'    => ['bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'label' => 'Pending'],
        'confirmed'  => ['bg' => 'bg-blue-100',    'text' => 'text-blue-800',    'label' => 'Confirmed'],
        'processing' => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-800',  'label' => 'Processing'],
        'completed'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
    ];
?>

<div class="min-h-screen py-6" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <a href="<?php echo e(route('seller.payments.index')); ?>"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>

        <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0">Pending Cash Collection</h1>
                <p class="text-sm text-gray-500 mb-0">All COD bookings with cash due — collect from customer</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl px-5 py-3 text-white">
                <p class="text-[10px] uppercase tracking-wider text-white/70 mb-0.5 mt-0">Total to Collect</p>
                <p class="text-xl font-bold mb-0">Rs. <?php echo e(number_format($totalDue, 2)); ?></p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        
        <div class="flex flex-wrap gap-2 mb-3">
            <a href="<?php echo e(route('seller.payments.pending-cod', array_filter(['q' => request('q')]))); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border
                      <?php echo e(!request('status') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'); ?>">
                All <span class="opacity-70">(<?php echo e(array_sum($statusCounts)); ?>)</span>
            </a>
            <?php $__currentLoopData = $statusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $sc = $statusColors[$key]; ?>
                <a href="<?php echo e(route('seller.payments.pending-cod', array_filter(['status' => $key, 'q' => request('q')]))); ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border
                          <?php echo e(request('status') === $key ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'); ?>">
                    <?php echo e($sc['label']); ?> <span class="opacity-70">(<?php echo e($count); ?>)</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-4 flex gap-2">
            <?php if(request('status')): ?>
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
            <?php endif; ?>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search booking, customer, phone..."
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Search</button>
            <?php if(request()->hasAny(['q', 'status'])): ?>
                <a href="<?php echo e(route('seller.payments.pending-cod')); ?>"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">Clear</a>
            <?php endif; ?>
        </form>

        <?php if($pending->isEmpty()): ?>
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-emerald-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-emerald-700 mb-1">🎉 No pending COD collections</p>
                <p class="text-xs text-gray-500 mb-0">
                    <?php echo e(request()->hasAny(['q', 'status']) ? 'Try clearing filters' : 'All COD bookings are fully paid'); ?>

                </p>
            </div>
        <?php else: ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $sc = $statusColors[$b->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($b->status)];
                        $canCollect = in_array($b->status, ['processing', 'completed']);
                    ?>
                    <div class="bg-white border border-amber-200 bg-amber-50/20 rounded-2xl p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3 flex-wrap">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide <?php echo e($sc['bg']); ?> <?php echo e($sc['text']); ?>">
                                        <?php echo e($sc['label']); ?>

                                    </span>
                                    <span class="text-xs font-mono text-gray-500"><?php echo e($b->reference); ?></span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-0.5 truncate">
                                    <?php echo e($b->customer_name); ?>

                                </p>
                                <p class="text-xs text-gray-500 mb-1">
                                    <a href="tel:<?php echo e($b->customer_phone); ?>" class="text-blue-600 hover:underline">📞 <?php echo e($b->customer_phone); ?></a>
                                    <?php if($b->branch): ?> · <?php echo e($b->branch->branch_name); ?> <?php endif; ?>
                                </p>
                                <p class="text-[11px] text-gray-400 mb-0">
                                    Booked <?php echo e($b->created_at->diffForHumans()); ?>

                                    <?php if((float) $b->paid_amount > 0): ?>
                                        · Advance: Rs. <?php echo e(number_format($b->paid_amount, 2)); ?>

                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-[10px] uppercase tracking-wider text-gray-500 mb-0.5 mt-0">To Collect</p>
                                <p class="text-lg font-bold text-amber-700 mb-0">Rs. <?php echo e(number_format($b->due_amount, 2)); ?></p>
                                <p class="text-[10px] text-gray-500 mb-0">Total: Rs. <?php echo e(number_format($b->total, 2)); ?></p>
                            </div>

                            <a href="<?php echo e(route('seller.bookings.show', $b->reference)); ?>"
                               class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white
                                      <?php echo e($canCollect ? 'bg-amber-600 hover:bg-amber-700' : 'bg-gray-500 hover:bg-gray-600'); ?> shrink-0 transition">
                                <?php echo e($canCollect ? 'Collect' : 'Manage'); ?>

                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <?php if(!$canCollect): ?>
                            <p class="text-[11px] text-amber-700 mt-2 pl-13 mb-0">
                                ⚠️ Mark booking as <strong>Processing</strong> or <strong>Completed</strong> first, then collect cash.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-4"><?php echo e($pending->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/seller/payments/pending-cod.blade.php ENDPATH**/ ?>