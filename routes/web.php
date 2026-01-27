<?php

use Illuminate\Support\Facades\Route;
use App\Models\Sale;
use App\Models\SaleDetails;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');


Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('/debug-sql', function () {
    $targetUserId = request()->user_id ?? 1;
    $targetSaleId = request()->sale_id ?? 1;

    $saleListQuery = Sale::where('user_id', $targetUserId)
        ->latest('id')
        ->with(["warehouse", "customer", 'payments.paymentType'])
        ->withCount('saleDetails')
        ->withSum('payments', 'amount');
    
    $saleItemsQuery = SaleDetails::where('sale_id', $targetSaleId)
        ->with(['product:id,name', 'productDetail:id,sku']);

    echo "<body style='background:#1a1a1a; color:#00ff00; font-family:monospace; padding:30px; line-height:1.6;'>";
    echo "<h1 style='color:#fff; border-bottom:2px solid #333; padding-bottom:10px;'>🔍 SQL Debugger</h1>";
    echo "<p style='color:#aaa;'>Usage: <code>/debug-sql?user_id=1&sale_id=5</code></p>";

    echo "<h3>1. Sale Listing Query (for User ID: $targetUserId)</h3>";
    echo "<pre style='background:#000; padding:20px; border-radius:8px; border:1px solid #444; overflow-x:auto; color:#0f0;'>";
    echo $saleListQuery->toRawSql() . ";";
    echo "</pre>";

    echo "<h3>2. Sale Items Query (for Sale ID: $targetSaleId)</h3>";
    echo "<pre style='background:#000; padding:20px; border-radius:8px; border:1px solid #444; overflow-x:auto; color:#0f0;'>";
    echo $saleItemsQuery->toRawSql() . ";";
    echo "</pre>";

    echo "<h3>3. Data Structure Preview</h3>";
    echo "<pre style='background:#222; padding:20px; border-radius:8px; border:1px solid #555; color:#fff;'>";
    try {
        $items = $saleItemsQuery->get()->map(fn($item) => [
            'product' => $item->product?->name,
            'sku'     => $item->productDetail?->sku,
            'quantity'=> $item->quantity,
            'subtotal'=> $item->subtotal
        ]);
        echo json_encode($items, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo "Local Execution Error: " . $e->getMessage();
    }
    echo "</pre>";
    echo "</body>";
});

Route::controller('SiteController')->group(function () {
    Route::post('subscribe', 'subscribe')->name('subscribe');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');

    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('/pwa-manifest', 'pwaManifest')->name('pwa.manifest');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blogs', 'blogs')->name('blogs');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('features', 'features')->name('features');
    Route::get('pricing-plan', 'pricingPlan')->name('pricing.plan');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
