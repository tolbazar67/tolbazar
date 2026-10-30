<?php $__env->startSection('title', $service->name . ' — TolBazar'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false},theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>

<div class="min-h-screen py-8" style="background:#f8fafc;font-family:Inter,system-ui,sans-serif;">
    <div class="max-w-6xl mx-auto px-4">

        
        <nav class="text-sm text-gray-500 mb-4 flex items-center gap-1.5 flex-wrap">
            <a href="<?php echo e(route('services.index')); ?>" class="hover:text-gray-900">Services</a>
            <span>/</span>
            <span class="text-gray-900 font-medium"><?php echo e($service->name); ?></span>
        </nav>

        
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1 mt-0"><?php echo e($service->name); ?></h1>
            <p class="text-sm text-gray-500 mb-0">
                <?php echo e($products->total()); ?> <?php echo e(\Illuminate\Support\Str::plural('product', $products->total())); ?> found
                <?php if($service->description): ?>
                    · <?php echo e($service->description); ?>

                <?php endif; ?>
            </p>
        </div>

        
        <form method="GET" class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-2">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search products..."
                   class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

            <select name="branch" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">All Branches</option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b->id); ?>" <?php if(request('branch') == $b->id): echo 'selected'; endif; ?>><?php echo e($b->branch_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <select name="sort" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="newest" <?php if(request('sort','newest')==='newest'): echo 'selected'; endif; ?>>Newest</option>
                <option value="price_low" <?php if(request('sort')==='price_low'): echo 'selected'; endif; ?>>Price: Low → High</option>
                <option value="price_high" <?php if(request('sort')==='price_high'): echo 'selected'; endif; ?>>Price: High → Low</option>
                <option value="featured" <?php if(request('sort')==='featured'): echo 'selected'; endif; ?>>Featured</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">Filter</button>

            <?php if(request()->hasAny(['q', 'branch', 'sort'])): ?>
                <a href="<?php echo e(route('services.products', $service->slug)); ?>"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                    Clear
                </a>
            <?php endif; ?>
        </form>

        
        <?php if($products->isEmpty()): ?>
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-600 mb-2">No products available</p>
                <p class="text-sm text-gray-500 mb-0">Try adjusting filters or check back later.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('services.product', [$service->slug, $p->slug])); ?>"
                       class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-blue-300 hover:shadow-lg transition flex flex-col">

                        
                        <div class="aspect-square bg-gray-100 overflow-hidden relative">
                            <?php if($p->thumbnail): ?>
                                <img src="<?php echo e(Storage::url($p->thumbnail)); ?>" alt="<?php echo e($p->name); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <?php if($p->is_featured): ?>
                                <span class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">★ Featured</span>
                            <?php endif; ?>
                            <?php if($p->track_stock && $p->stock_quantity <= 0): ?>
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold uppercase">Out of Stock</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="p-3 flex-1 flex flex-col">
                            <p class="text-xs text-gray-500 mb-1"><?php echo e($p->branch?->branch_name); ?></p>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 flex-1 group-hover:text-blue-700"><?php echo e($p->name); ?></h3>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-base font-bold text-gray-900">Rs. <?php echo e(number_format($p->price, 2)); ?></span>
                                <?php if($p->compare_price && $p->compare_price > $p->price): ?>
                                    <span class="text-xs text-gray-400 line-through">Rs. <?php echo e(number_format($p->compare_price, 0)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="mt-6"><?php echo e($products->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/public/service-products.blade.php ENDPATH**/ ?>