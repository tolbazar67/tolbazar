<?php $__env->startSection('title', 'Transaction History — Wallet'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-5xl mx-auto px-4">

        <a href="<?php echo e(route('wallet.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 mt-0">Transaction History</h1>
            <p class="text-sm text-gray-500 mb-0">Balance: <strong>Rs. <?php echo e(number_format($wallet->balance, 2)); ?></strong></p>
        </div>

        
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-4">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                <select name="type" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">All Types</option>
                    <option value="credit" <?php if(request('type') === 'credit'): echo 'selected'; endif; ?>>Credit</option>
                    <option value="debit" <?php if(request('type') === 'debit'): echo 'selected'; endif; ?>>Debit</option>
                </select>
                <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">All Statuses</option>
                    <option value="completed" <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>Completed</option>
                    <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>Pending</option>
                    <option value="failed" <?php if(request('status') === 'failed'): echo 'selected'; endif; ?>>Failed</option>
                </select>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            </div>
        </form>

        
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 flex items-center gap-3 hover:bg-gray-50/50 transition <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?>">
                    <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center <?php echo e($txn->type === 'credit' ? 'bg-emerald-50' : 'bg-red-50'); ?>">
                        <?php if($txn->type === 'credit'): ?>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 mb-0 truncate"><?php echo e($txn->description ?: $txn->category_label); ?></p>
                        <p class="text-xs text-gray-500 mb-0">
                            <span class="font-mono"><?php echo e($txn->transaction_id); ?></span>
                            · <?php echo e($txn->created_at->format('d M Y, h:i A')); ?>

                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold mb-0 <?php echo e($txn->type === 'credit' ? 'text-emerald-600' : 'text-red-600'); ?>">
                            <?php echo e($txn->type === 'credit' ? '+' : '−'); ?> Rs. <?php echo e(number_format($txn->amount, 2)); ?>

                        </p>
                        <span class="inline-block mt-1 text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded
                            <?php if($txn->status === 'completed'): ?> text-emerald-700 bg-emerald-100
                            <?php elseif($txn->status === 'pending'): ?> text-amber-700 bg-amber-100
                            <?php else: ?> text-red-700 bg-red-100 <?php endif; ?>">
                            <?php echo e($txn->status); ?>

                        </span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-12 text-center">
                    <p class="text-sm text-gray-500 mb-0">No transactions found.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4"><?php echo e($transactions->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/wallet/transactions.blade.php ENDPATH**/ ?>