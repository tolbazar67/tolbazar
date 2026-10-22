

<?php $__env->startSection('title', 'Services'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Services</li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* =====================================================
   TOKENS
   One type scale, one radius scale, one column system.
   ===================================================== */
.svc{
    --line:#e4e7ec;
    --line-strong:#cfd4dc;
    --ink:#101828;
    --ink2:#475467;
    --mute:#98a2b3;
    --soft:#f9fafb;
    --accent:#f59e0b;
    --accent-ink:#b45309;
    --accent-soft:#fffbeb;
    --danger:#d92d20;
    --ok:#067647;
    --ok-soft:#dcfae6;
    --navy:#0b1120;

    --t-xs:.75rem;
    --t-sm:.8125rem;
    --t-md:.9375rem;
    --t-xl:1.5rem;

    --r-sm:8px;
    --r-md:10px;
    --r-lg:14px;

    --pad-x:1.1rem;
    --gap:1rem;

    font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif;
    font-size:var(--t-sm);
    color:var(--ink);
    -webkit-font-smoothing:antialiased;
    font-variant-numeric:tabular-nums;
}
.svc *,.svc *::before,.svc *::after{box-sizing:border-box}
.svc :focus-visible{outline:2px solid var(--accent);outline-offset:2px;border-radius:6px}

/* =====================================================
   PAGE HEADER
   ===================================================== */
.svc .sec-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:1rem;
    flex-wrap:wrap;
    margin-bottom:1.25rem;
}
.svc .head-title{
    margin:0;
    font-size:var(--t-xl);
    font-weight:700;
    letter-spacing:-.025em;
    line-height:1.2;
}
.svc .sec-head p{
    margin:.3rem 0 0;
    color:var(--ink2);
    font-size:var(--t-sm);
}
.svc .btn-create{
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    height:40px;
    padding:0 1.05rem;
    background:var(--navy);
    color:#fff;
    border:0;
    border-radius:var(--r-md);
    font-size:var(--t-sm);
    font-weight:600;
    text-decoration:none;
    transition:background .15s,transform .15s;
}
.svc .btn-create i{color:var(--accent);font-size:.95rem}
.svc .btn-create:hover{background:#1d2740;color:#fff;transform:translateY(-1px)}

/* =====================================================
   OVERVIEW (quiet summary strip)
   ===================================================== */
.svc .overview{
    display:grid;
    grid-template-columns:auto minmax(0,1fr);
    gap:2rem;
    align-items:center;
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--r-lg);
    padding:1.15rem 1.4rem;
    margin-bottom:1.25rem;
}
.svc .ov-total{
    padding-right:2rem;
    border-right:1px solid var(--line);
}
.svc .ov-total strong{
    display:block;
    font-size:2.25rem;
    font-weight:700;
    line-height:1;
    letter-spacing:-.04em;
}
.svc .ov-total span{
    display:block;
    margin-top:.4rem;
    color:var(--ink2);
    font-size:var(--t-sm);
    font-weight:500;
}
.svc .meter{
    display:flex;
    height:8px;
    border-radius:99px;
    overflow:hidden;
    background:#eef0f4;
}
.svc .meter i{display:block;height:100%;transition:width .5s ease}
.svc .legend{
    display:flex;
    flex-wrap:wrap;
    gap:.5rem 1.75rem;
    margin-top:.85rem;
}
.svc .legend div{
    display:flex;
    align-items:center;
    gap:.4rem;
    color:var(--ink2);
    font-size:var(--t-sm);
    font-weight:500;
}
.svc .legend b{color:var(--ink);font-size:var(--t-md);font-weight:700}
.svc .dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* =====================================================
   TOOLBAR
   ===================================================== */
.svc .toolbar{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:.6rem;
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--r-lg);
    padding:.6rem;
    margin-bottom:1rem;
}
.svc .toolbar > *{height:38px}
.svc .search{flex:1 1 240px;position:relative}
.svc .search i{
    position:absolute;left:.85rem;top:50%;
    transform:translateY(-50%);
    color:var(--mute);font-size:.875rem;pointer-events:none;
}
.svc .search input{
    width:100%;height:100%;
    padding:0 2.5rem 0 2.35rem;
    border:1px solid var(--line);
    background:var(--soft);
    border-radius:var(--r-sm);
    font-size:var(--t-sm);
    color:var(--ink);
    transition:border-color .15s,box-shadow .15s,background .15s;
}
.svc .search input::placeholder{color:var(--mute)}
.svc .search input:focus{
    outline:0;background:#fff;border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(245,158,11,.15);
}
.svc .search kbd{
    position:absolute;right:.6rem;top:50%;transform:translateY(-50%);
    padding:.05rem .4rem;
    background:#fff;border:1px solid var(--line);border-radius:5px;
    color:var(--mute);font-size:var(--t-xs);font-weight:600;font-family:inherit;
}
.svc .chips{
    display:flex;gap:2px;padding:3px;
    background:var(--soft);
    border:1px solid var(--line);
    border-radius:var(--r-md);
}
.svc .chip{
    border:0;background:transparent;
    padding:0 .75rem;
    border-radius:7px;
    color:var(--ink2);
    font-size:var(--t-sm);font-weight:600;
    cursor:pointer;
    transition:background .15s,color .15s;
}
.svc .chip:hover:not(.on){color:var(--ink)}
.svc .chip.on{background:#fff;color:var(--ink);box-shadow:0 1px 3px rgba(16,24,40,.1)}
.svc .chip span{margin-left:.35rem;color:var(--mute);font-size:var(--t-xs);font-weight:600}
.svc .chip.on span{color:var(--accent-ink)}
.svc select.sort{
    padding:0 .75rem;
    border:1px solid var(--line);
    border-radius:var(--r-sm);
    background:#fff;color:var(--ink);
    font-size:var(--t-sm);font-weight:600;cursor:pointer;
}
.svc select.sort:focus{outline:0;border-color:var(--accent);box-shadow:0 0 0 3px rgba(245,158,11,.15)}
.svc .views{
    display:flex;overflow:hidden;
    border:1px solid var(--line);
    border-radius:var(--r-sm);background:#fff;
}
.svc .views button{
    width:38px;border:0;background:transparent;
    color:var(--mute);cursor:pointer;font-size:.95rem;
    transition:background .15s,color .15s;
}
.svc .views button:hover{color:var(--ink)}
.svc .views button.on{background:var(--navy);color:var(--accent)}
.svc .toolbar .ib{height:38px;width:38px}

/* =====================================================
   BULK BAR
   ===================================================== */
.svc .bulk{
    display:none;
    align-items:center;
    gap:.6rem;
    background:var(--accent-soft);
    border:1px solid #fcd34d;
    border-radius:var(--r-md);
    padding:.55rem .9rem;
    margin-bottom:1rem;
    font-size:var(--t-sm);
}
.svc .bulk.show{display:flex}
.svc .bulk strong{color:#92400e;margin-right:.35rem}
.svc .bulk button{
    height:30px;padding:0 .75rem;
    background:#fff;color:#92400e;
    border:1px solid #fcd34d;border-radius:7px;
    font-size:var(--t-xs);font-weight:600;cursor:pointer;
    transition:background .15s;
}
.svc .bulk button:hover{background:#fef3c7}
.svc .bulk button.danger{color:var(--danger);border-color:#fecaca}
.svc .bulk button.danger:hover{background:#fef2f2}

/* =====================================================
   LIST VIEW  (single source of truth for column widths)
   Row border-left (4px) + 1px right border are mirrored in
   the header padding so labels sit exactly over the data.
   ===================================================== */
.svc .list{display:flex;flex-direction:column;gap:.5rem}

.svc .list-head,
.svc .row-item{
    display:grid;
    grid-template-columns:28px minmax(0,1fr) 84px 84px 84px 108px 112px;
    grid-template-areas:"pick main prod br ord status acts";
    column-gap:var(--gap);
    align-items:center;
}
.svc .list.no-bulk .list-head,
.svc .list.no-bulk .row-item{
    grid-template-columns:minmax(0,1fr) 84px 84px 84px 108px 112px;
    grid-template-areas:"main prod br ord status acts";
}
.svc .list-head{
    padding:.2rem calc(var(--pad-x) + 1px) .2rem calc(var(--pad-x) + 4px);
    color:var(--mute);
    font-size:var(--t-xs);
    font-weight:600;
}
.svc .list-head .h-status{justify-self:start}
.svc .list-head .h-acts{justify-self:end}

.svc .h-pick,.svc .c-pick{grid-area:pick}
.svc .h-main,.svc .c-main{grid-area:main}
.svc .h-prod,.svc .c-prod{grid-area:prod}
.svc .h-br,.svc .c-br{grid-area:br}
.svc .h-ord,.svc .c-ord{grid-area:ord}
.svc .h-status,.svc .c-status{grid-area:status}
.svc .h-acts,.svc .acts{grid-area:acts}

.svc .row-item{
    --c:var(--accent);
    position:relative;
    min-height:76px;
    padding:.8rem var(--pad-x);
    background:#fff;
    border:1px solid var(--line);
    border-left:4px solid var(--c);
    border-radius:var(--r-lg);
    transition:border-color .15s,box-shadow .15s,background .15s;
}
.svc .row-item:hover{
    border-color:var(--line-strong);
    border-left-color:var(--c);
    box-shadow:0 4px 14px rgba(16,24,40,.07);
}
.svc .row-item.sel{background:var(--accent-soft);border-color:#fcd34d;border-left-color:var(--c)}
.svc .row-item.is-off .ico,
.svc .row-item.is-off .name{opacity:.55}

/* checkbox cell */
.svc .c-pick{display:grid;place-items:center}
.svc input[type=checkbox]{
    width:16px;height:16px;margin:0;
    accent-color:var(--accent);cursor:pointer;
}

/* main cell */
.svc .c-main{display:flex;align-items:center;gap:.85rem;min-width:0}
.svc .ico{
    flex:0 0 44px;
    width:44px;height:44px;
    display:grid;place-items:center;
    border-radius:var(--r-md);
    background:color-mix(in srgb,var(--c) 13%,#fff);
    color:var(--c);
    font-size:1.2rem;
}
.svc .info{min-width:0;flex:1}
.svc .name{
    display:flex;align-items:center;flex-wrap:wrap;gap:.5rem;
    font-size:var(--t-md);font-weight:600;
    letter-spacing:-.01em;line-height:1.3;
}
.svc .desc{
    margin-top:.15rem;
    color:var(--ink2);
    font-size:var(--t-sm);line-height:1.45;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.svc .slug{
    display:inline-block;margin-top:.4rem;
    padding:.1rem .45rem;
    background:var(--soft);
    border:1px solid var(--line);
    border-radius:6px;
    color:var(--ink2);
    font:600 var(--t-xs)/1.4 ui-monospace,'SF Mono',Menlo,Consolas,monospace;
    cursor:pointer;
    transition:border-color .15s,color .15s,background .15s;
}
.svc .slug:hover{border-color:var(--accent);color:var(--accent-ink);background:var(--accent-soft)}
.svc .tag-feat{
    display:inline-flex;align-items:center;gap:.25rem;
    padding:.1rem .5rem;
    background:#fef3c7;color:#92400e;
    border-radius:99px;
    font-size:var(--t-xs);font-weight:600;line-height:1.4;
}
.svc .tag-feat i{font-size:.65rem}

/* stat cells: number in list, number + label in grid */
.svc .c-stats{display:contents}
.svc .c-stat{min-width:0}
.svc .c-stat b{
    display:block;
    font-size:var(--t-md);font-weight:600;line-height:1.2;
    color:var(--ink);
}
.svc .c-stat span{display:none;margin-top:.15rem;color:var(--mute);font-size:var(--t-xs);font-weight:500}
.svc .c-stat.is-zero b{color:var(--mute);font-weight:500}

/* status */
.svc .c-status form{margin:0}
.svc .pill{
    display:inline-flex;align-items:center;justify-content:center;gap:.4rem;
    min-width:92px;height:28px;padding:0 .8rem;
    border:0;border-radius:99px;
    font-size:var(--t-xs);font-weight:600;
    cursor:pointer;
    transition:background .15s;
}
.svc .pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.svc .pill.on{background:var(--ok-soft);color:var(--ok)}
.svc .pill.on:hover{background:#abefc6}
.svc .pill.off{background:#eaecf0;color:var(--ink2)}
.svc .pill.off:hover{background:#d0d5dd}

/* action buttons */
.svc .acts{display:flex;align-items:center;justify-content:flex-end;gap:.3rem}
.svc .acts form{margin:0;display:flex}
.svc .ib{
    display:grid;place-items:center;
    width:34px;height:34px;padding:0;
    background:#fff;color:var(--ink2);
    border:1px solid var(--line);border-radius:var(--r-sm);
    font-size:.875rem;line-height:1;
    text-decoration:none;cursor:pointer;
    transition:border-color .15s,color .15s,background .15s;
}
.svc .ib:hover{border-color:var(--navy);color:var(--navy);background:var(--soft)}
.svc .ib.star.on{color:var(--accent);border-color:#fcd34d;background:var(--accent-soft)}
.svc .ib.del:hover{border-color:var(--danger);color:var(--danger);background:#fef3f2}

/* =====================================================
   GRID VIEW
   ===================================================== */
.svc .list.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(310px,1fr));
    gap:1rem;
}
.svc .list.grid .list-head{display:none}

.svc .list.grid .row-item,
.svc .list.grid.no-bulk .row-item{
    grid-template-columns:minmax(0,1fr) auto;
    grid-template-areas:"main main" "stats stats" "status acts";
    row-gap:1rem;
    align-content:start;
    align-items:center;
    min-height:0;
    padding:1.1rem 1.15rem;
    border-left:1px solid var(--line);
    border-top:4px solid var(--c);
}
.svc .list.grid .row-item:hover{border-left-color:var(--line-strong);border-top-color:var(--c)}
.svc .list.grid .row-item.sel{border-left-color:#fcd34d;border-top-color:var(--c)}
.svc .list.grid .c-pick{position:absolute;top:.9rem;right:.9rem}
.svc .list.grid.has-bulk .c-main{padding-right:1.6rem}
.svc .list.grid .desc{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.svc .list.grid .c-stats{
    grid-area:stats;
    display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;
    padding:.8rem 0 0;
    border-top:1px solid var(--line);
}
.svc .list.grid .c-stat span{display:block}

/* =====================================================
   EMPTY + PAGER
   ===================================================== */
.svc .empty{
    text-align:center;
    padding:3rem 1.5rem;
    background:#fff;
    border:1.5px dashed var(--line-strong);
    border-radius:var(--r-lg);
}
.svc .empty i{display:block;margin-bottom:.5rem;color:var(--mute);font-size:2.25rem}
.svc .empty h5{margin:.4rem 0 .3rem;font-size:var(--t-md);font-weight:600}
.svc .empty p{max-width:380px;margin:0 auto 1.1rem;color:var(--ink2);font-size:var(--t-sm);line-height:1.5}
.svc .pager{display:flex;justify-content:center;margin-top:1.5rem}

/* =====================================================
   TOAST + CONFIRM MODAL
   ===================================================== */
#svcToast{
    position:fixed;bottom:1.5rem;left:50%;
    transform:translate(-50%,12px);
    padding:.6rem 1.1rem;
    background:#0b1120;color:#fff;
    border-radius:var(--r-md,10px);
    font:600 .8125rem 'Inter',system-ui,sans-serif;
    opacity:0;pointer-events:none;
    transition:opacity .25s,transform .25s;
    z-index:2000;
    box-shadow:0 12px 32px rgba(11,17,32,.3);
}
#svcToast.show{opacity:1;transform:translate(-50%,0)}
#svcModal{
    position:fixed;inset:0;z-index:2000;
    display:none;place-items:center;padding:1rem;
    background:rgba(11,17,32,.5);
    backdrop-filter:blur(3px);
    font-family:'Inter',system-ui,sans-serif;
}
#svcModal.show{display:grid}
#svcModal .box{
    width:100%;max-width:420px;padding:1.5rem;
    background:#fff;border-radius:14px;
    box-shadow:0 24px 60px rgba(11,17,32,.3);
}
#svcModal .warn{
    display:grid;place-items:center;
    width:44px;height:44px;margin-bottom:1rem;
    background:#fee4e2;color:#d92d20;
    border-radius:12px;font-size:1.2rem;
}
#svcModal h5{margin:0 0 .35rem;font-size:1.0625rem;font-weight:600;letter-spacing:-.01em}
#svcModal p{margin:0;color:#475467;font-size:.875rem;line-height:1.55}
#svcModal .bt{display:flex;justify-content:flex-end;gap:.5rem;margin-top:1.3rem}

/* =====================================================
   RESPONSIVE  (list collapses to the same card layout)
   ===================================================== */
@media(max-width:991.98px){
    .svc .overview{grid-template-columns:1fr;gap:1rem;padding:1.1rem}
    .svc .ov-total{padding:0 0 1rem;border-right:0;border-bottom:1px solid var(--line)}

    .svc .list-head{display:none}
    .svc .row-item,
    .svc .list.no-bulk .row-item{
        grid-template-columns:minmax(0,1fr) auto;
        grid-template-areas:"main main" "stats stats" "status acts";
        row-gap:.9rem;
        padding:1rem var(--pad-x);
    }
    .svc .c-pick{position:absolute;top:.9rem;right:.9rem}
    .svc .list.has-bulk .c-main{padding-right:1.6rem}
    .svc .c-stats{
        grid-area:stats;
        display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;
        padding-top:.8rem;border-top:1px solid var(--line);
    }
    .svc .c-stat span{display:block}
    .svc .desc{white-space:normal}
}
@media(max-width:575.98px){
    .svc .toolbar{padding:.5rem}
    .svc .chips{width:100%}
    .svc .chip{flex:1;height:100%}
    .svc .list.grid{grid-template-columns:1fr}
}
@media(prefers-reduced-motion:reduce){
    .svc *,#svcToast{transition:none!important}
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $total = max($stats['total'], 1);
    $pctActive = round($stats['active'] / $total * 100);
    $pctInactive = 100 - $pctActive;
    $hasBulk = \Illuminate\Support\Facades\Route::has('admin.services.bulk');
?>

<div class="svc">

    
    <div class="sec-head">
        <div>
            <h2 class="head-title">Service categories</h2>
            <p>Manage what customers see, what's featured, and the display order.</p>
        </div>
        <a href="<?php echo e(route('admin.services.create')); ?>" class="btn-create">
            <i class="bi bi-plus-lg"></i> Create service
        </a>
    </div>

    
    <section class="overview" aria-label="Services summary">
        <div class="ov-total">
            <strong><?php echo e($stats['total']); ?></strong>
            <span>Services on platform</span>
        </div>
        <div>
            <div class="meter" role="img" aria-label="<?php echo e($pctActive); ?>% of services are active">
                <i style="width:<?php echo e($pctActive); ?>%;background:#12b76a"></i>
                <i style="width:<?php echo e($pctInactive); ?>%;background:#cfd4dc"></i>
            </div>
            <div class="legend">
                <div><span class="dot" style="background:#12b76a"></span><b><?php echo e($stats['active']); ?></b> Active</div>
                <div><span class="dot" style="background:#98a2b3"></span><b><?php echo e($stats['inactive']); ?></b> Inactive</div>
                <div><span class="dot" style="background:#f59e0b"></span><b><?php echo e($stats['featured']); ?></b> Featured</div>
            </div>
        </div>
    </section>

    
    <form method="GET" class="toolbar" id="filterForm">
        <div class="search">
            <i class="bi bi-search"></i>
            <input type="text" name="search" id="q" value="<?php echo e(request('search')); ?>" placeholder="Search by name or slug" autocomplete="off" aria-label="Search services">
            <kbd>/</kbd>
        </div>
        <input type="hidden" name="status" id="statusInput" value="<?php echo e(request('status')); ?>">
        <div class="chips" role="group" aria-label="Filter by status">
            <button type="button" class="chip <?php echo e(!request('status') ? 'on' : ''); ?>" data-status="">All<span><?php echo e($stats['total']); ?></span></button>
            <button type="button" class="chip <?php echo e(request('status') === 'active' ? 'on' : ''); ?>" data-status="active">Active<span><?php echo e($stats['active']); ?></span></button>
            <button type="button" class="chip <?php echo e(request('status') === 'inactive' ? 'on' : ''); ?>" data-status="inactive">Inactive<span><?php echo e($stats['inactive']); ?></span></button>
        </div>
        <select class="sort" id="sortBy" aria-label="Sort services">
            <option value="order">Display order</option>
            <option value="name">Name A–Z</option>
            <option value="products">Most products</option>
            <option value="branches">Most branches</option>
        </select>
        <div class="views" role="group" aria-label="Layout">
            <button type="button" data-view="list" class="on" title="List view" aria-label="List view"><i class="bi bi-list-ul"></i></button>
            <button type="button" data-view="grid" title="Grid view" aria-label="Grid view"><i class="bi bi-grid-3x3-gap"></i></button>
        </div>
        <?php if(request()->hasAny(['search','status'])): ?>
            <a href="<?php echo e(route('admin.services.index')); ?>" class="ib" title="Clear filters" aria-label="Clear filters"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
    </form>

    <?php if($hasBulk): ?>
    <form method="POST" action="<?php echo e(route('admin.services.bulk')); ?>" id="bulkForm" class="bulk">
        <?php echo csrf_field(); ?>
        <strong><span id="selCount">0</span> selected</strong>
        <button type="submit" name="action" value="activate">Activate</button>
        <button type="submit" name="action" value="deactivate">Deactivate</button>
        <button type="submit" name="action" value="delete" class="danger" onclick="return confirm('Delete selected services?')">Delete</button>
        <span id="bulkIds"></span>
    </form>
    <?php endif; ?>

    <?php if($services->count()): ?>
    <div class="list <?php echo e($hasBulk ? 'has-bulk' : 'no-bulk'); ?>" id="list">

        
        <div class="list-head" aria-hidden="true">
            <?php if($hasBulk): ?>
                <div class="h-pick"><input type="checkbox" id="pickAll" aria-label="Select all services" tabindex="-1"></div>
            <?php endif; ?>
            <div class="h-main">Service</div>
            <div class="h-status">Status</div>
            <div class="h-acts">Actions</div>
        </div>

        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $color = $service->color ?: '#f59e0b';
                $pc = $service->products_count ?? $service->products()->count();
                $bc = $service->branches_count ?? $service->branches()->count();
            ?>
            <article class="row-item <?php echo e($service->is_active ? '' : 'is-off'); ?>" style="--c:<?php echo e($color); ?>"
                 data-id="<?php echo e($service->id); ?>" data-name="<?php echo e(strtolower($service->name)); ?>" data-slug="<?php echo e(strtolower($service->slug)); ?>"
                 data-order="<?php echo e($service->sort_order); ?>" data-products="<?php echo e($pc); ?>" data-branches="<?php echo e($bc); ?>">

                <?php if($hasBulk): ?>
                    <div class="c-pick"><input type="checkbox" class="pick" aria-label="Select <?php echo e($service->name); ?>"></div>
                <?php endif; ?>

                <div class="c-main">
                    <div class="ico" aria-hidden="true"><i class="<?php echo e($service->icon ?: 'bi bi-grid'); ?>"></i></div>
                    <div class="info">
                        <div class="name">
                            <?php echo e($service->name); ?>

                            <?php if($service->is_featured): ?><span class="tag-feat"><i class="bi bi-star-fill"></i> Featured</span><?php endif; ?>
                        </div>
                        <?php if($service->description): ?><div class="desc" title="<?php echo e($service->description); ?>"><?php echo e(Str::limit($service->description, 90)); ?></div><?php endif; ?>
                        <button type="button" class="slug" data-copy="<?php echo e($service->slug); ?>" title="Click to copy"><?php echo e($service->slug); ?></button>
                    </div>
                </div>

                <div class="c-status">
                    <form action="<?php echo e(route('admin.services.toggle-active', $service)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="pill <?php echo e($service->is_active ? 'on' : 'off'); ?>" title="Click to <?php echo e($service->is_active ? 'deactivate' : 'activate'); ?>">
                            <?php echo e($service->is_active ? 'Active' : 'Inactive'); ?>

                        </button>
                    </form>
                </div>

                <div class="acts">
                    <a href="<?php echo e(route('admin.services.edit', $service)); ?>" class="ib" title="Edit" aria-label="Edit <?php echo e($service->name); ?>"><i class="bi bi-pencil"></i></a>
                    <form action="<?php echo e(route('admin.services.toggle-featured', $service)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="ib star <?php echo e($service->is_featured ? 'on' : ''); ?>" title="<?php echo e($service->is_featured ? 'Remove from featured' : 'Mark as featured'); ?>" aria-label="<?php echo e($service->is_featured ? 'Remove from featured' : 'Mark as featured'); ?>">
                            <i class="bi bi-star<?php echo e($service->is_featured ? '-fill' : ''); ?>"></i>
                        </button>
                    </form>
                    <form action="<?php echo e(route('admin.services.destroy', $service)); ?>" method="POST" class="del-form" data-name="<?php echo e($service->name); ?>" data-products="<?php echo e($pc); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="button" class="ib del" title="Delete" aria-label="Delete <?php echo e($service->name); ?>"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="empty" id="noMatch" style="display:none">
        <i class="bi bi-search"></i>
        <h5>No match on this page</h5>
        <p>Try another name or slug, or clear the search.</p>
    </div>
    <?php else: ?>
    <div class="empty">
        <i class="bi bi-inbox"></i>
        <h5>No services found</h5>
        <p><?php echo e(request()->hasAny(['search','status']) ? 'Nothing matches these filters. Clear them to see all services.' : 'Create your first service to start grouping products and branches.'); ?></p>
        <a href="<?php echo e(route('admin.services.create')); ?>" class="btn-create"><i class="bi bi-plus-lg"></i> Create service</a>
    </div>
    <?php endif; ?>

    <?php if($services->hasPages()): ?>
        <div class="pager"><?php echo e($services->withQueryString()->links()); ?></div>
    <?php endif; ?>
</div>

<div id="svcModal" role="dialog" aria-modal="true" aria-labelledby="delTitle">
    <div class="box">
        <div class="warn"><i class="bi bi-trash3"></i></div>
        <h5 id="delTitle">Delete this service?</h5>
        <p id="delText"></p>
        <div class="bt">
            <button type="button" class="btn btn-light" id="delCancel">Cancel</button>
            <button type="button" class="btn btn-danger" id="delGo">Delete service</button>
        </div>
    </div>
</div>
<div id="svcToast" role="status" aria-live="polite"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){
    const $ = (s, r=document) => r.querySelector(s), $$ = (s, r=document) => [...r.querySelectorAll(s)];
    const list = $('#list'); if(!list) return;
    const q = $('#q'), items = $$('.row-item', list);
    const isShown = el => el.style.display !== 'none';
    const toast = m => { const t = $('#svcToast'); t.textContent = m; t.classList.add('show'); clearTimeout(toast.id); toast.id = setTimeout(() => t.classList.remove('show'), 1800); };

    /* ---- bulk selection ---- */
    const bulk = $('#bulkForm'), pickAll = $('#pickAll');
    const refresh = () => {
        if(!bulk) return;
        const on = items.filter(r => isShown(r) && r.querySelector('.pick').checked);
        $('#selCount').textContent = on.length;
        bulk.classList.toggle('show', on.length > 0);
        items.forEach(r => r.classList.toggle('sel', isShown(r) && r.querySelector('.pick').checked));
        $('#bulkIds').innerHTML = on.map(r => '<input type="hidden" name="ids[]" value="' + r.dataset.id + '">').join('');
        if(pickAll){
            const vis = items.filter(isShown);
            pickAll.checked = vis.length > 0 && on.length === vis.length;
            pickAll.indeterminate = on.length > 0 && on.length < vis.length;
        }
    };
    if(bulk){
        $$('.pick').forEach(c => c.addEventListener('change', refresh));
        if(pickAll){
            pickAll.tabIndex = 0;
            pickAll.parentElement.parentElement.removeAttribute('aria-hidden');
            pickAll.addEventListener('change', () => {
                items.filter(isShown).forEach(r => r.querySelector('.pick').checked = pickAll.checked);
                refresh();
            });
        }
    }

    /* ---- live search ---- */
    q.addEventListener('input', () => {
        const v = q.value.trim().toLowerCase(); let n = 0;
        items.forEach(i => {
            const hit = !v || i.dataset.name.includes(v) || i.dataset.slug.includes(v);
            i.style.display = hit ? '' : 'none'; if(hit) n++;
        });
        $('#noMatch').style.display = n ? 'none' : 'block';
        refresh();
    });
    document.addEventListener('keydown', e => {
        if(e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)){ e.preventDefault(); q.focus(); }
    });

    /* ---- status chips ---- */
    $$('.chip').forEach(c => c.addEventListener('click', () => { $('#statusInput').value = c.dataset.status; $('#filterForm').submit(); }));

    /* ---- sort (this page only) ---- */
    const sortFns = {
        order:    (a,b) => a.dataset.order - b.dataset.order,
        name:     (a,b) => a.dataset.name.localeCompare(b.dataset.name),
        products: (a,b) => b.dataset.products - a.dataset.products,
        branches: (a,b) => b.dataset.branches - a.dataset.branches
    };
    $('#sortBy').addEventListener('change', e => [...items].sort(sortFns[e.target.value]).forEach(i => list.appendChild(i)));

    /* ---- list / grid view ---- */
    function setView(v){
        list.classList.toggle('grid', v === 'grid');
        $$('.views button').forEach(b => b.classList.toggle('on', b.dataset.view === v));
        try{ localStorage.setItem('svcView', v); }catch(e){}
    }
    $$('.views button').forEach(b => b.addEventListener('click', () => setView(b.dataset.view)));
    try{ setView(localStorage.getItem('svcView') || 'list'); }catch(e){}

    /* ---- copy slug ---- */
    $$('[data-copy]').forEach(s => s.addEventListener('click', () => {
        if(!navigator.clipboard) return;
        navigator.clipboard.writeText(s.dataset.copy).then(() => toast('Slug copied: ' + s.dataset.copy));
    }));

    /* ---- delete confirm ---- */
    let pending = null; const modal = $('#svcModal');
    $$('.del-form .del').forEach(b => b.addEventListener('click', () => {
        pending = b.closest('form'); const p = +pending.dataset.products;
        $('#delText').textContent = '"' + pending.dataset.name + '" will be removed.' + (p ? ' Its ' + p + ' product' + (p > 1 ? 's' : '') + ' will be detached from this service.' : '');
        modal.classList.add('show'); $('#delCancel').focus();
    }));
    const close = () => { modal.classList.remove('show'); pending = null; };
    $('#delCancel').addEventListener('click', close);
    $('#delGo').addEventListener('click', () => pending && pending.submit());
    modal.addEventListener('click', e => { if(e.target === modal) close(); });
    document.addEventListener('keydown', e => { if(e.key === 'Escape') close(); });
})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tolbazar\resources\views/admin/services/index.blade.php ENDPATH**/ ?>