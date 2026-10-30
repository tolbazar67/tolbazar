<?php $__env->startSection('title', 'Transfer Money — Wallet'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>

<div class="min-h-screen py-6 md:py-10" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-2xl mx-auto px-4">

        <a href="<?php echo e(route('wallet.dashboard')); ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Wallet
        </a>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-blue-400 to-indigo-600"></div>
            <div class="p-6 md:p-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 mt-0">Transfer Money</h1>
                <p class="text-sm text-gray-500 mb-5">Available: <strong>Rs. <?php echo e(number_format($wallet->balance, 2)); ?></strong></p>

                <?php if(session('success')): ?>
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('wallet.transfer.submit')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Recipient <span class="text-red-500">*</span></label>
                        <input type="text" name="recipient" value="<?php echo e(old('recipient')); ?>" required
                               placeholder="Enter @username, email or mobile number"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                        <p class="text-xs text-gray-500 mt-1 mb-0">User must have a TolBazar account</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount (NPR) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="<?php echo e(old('amount')); ?>" min="10" step="0.01" required
                               placeholder="1000"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note (optional)</label>
                        <input type="text" name="note" value="<?php echo e(old('note')); ?>" maxlength="200" placeholder="What's this for?"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Wallet PIN <span class="text-red-500">*</span></label>
                        <input type="password" name="pin" required pattern="\d{6}" maxlength="6" inputmode="numeric" placeholder="••••••"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm text-center font-mono tracking-widest focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Send Money
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/wallet/transfer.blade.php ENDPATH**/ ?>