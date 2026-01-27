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
    // 1. Check Authentication State
    $user = auth()->user();
    $parent = getParentUser();
    
    // 2. Determine target for debugging
    $targetUserId = request()->user_id ?? ($parent ? $parent->id : 1);
    $targetSaleId = request()->sale_id ?? 1;

    echo "<body style='background:#1a1a1a; color:#eee; font-family:monospace; padding:30px; line-height:1.6;'>";
    echo "<h1 style='color:#fff; border-bottom:2px solid #333; padding-bottom:10px;'>🔍 Session & SQL Debugger</h1>";

    // --- AUTHENTICATION SECTION ---
    echo "<div style='background:#222; padding:20px; border-radius:8px; border:1px solid #444; margin-bottom:20px;'>";
    echo "<h3 style='color:#ff9f43; margin-top:0;'>👤 Current Session Auth</h3>";
    if ($user) {
        echo "✅ Logged in as: <b>" . $user->email . "</b> (ID: " . $user->id . ")<br>";
        echo "🏢 Parent Store ID: <b>" . ($parent ? $parent->id : 'None') . "</b> (This is what determines which sales you see)";
    } else {
        echo "❌ <b style='color:#ff4757;'>You are NOT logged in.</b> Please log in to the user panel first.";
    }
    echo "</div>";

    // --- QUERY SECTION ---
    if ($parent) {
        $saleListQuery = Sale::where('user_id', $targetUserId);
        $totalSalesForThisUser = (clone $saleListQuery)->count();

        echo "<h3>1. Sale Listing Query (Filtering for ID: $targetUserId)</h3>";
        echo "<p style='color:#aaa;'>This user has <b>$totalSalesForThisUser</b> sales in the database.</p>";
        echo "<pre style='background:#000; padding:20px; border-radius:8px; border:1px solid #444; overflow-x:auto; color:#0f0;'>";
        echo $saleListQuery->latest('id')->toRawSql() . ";";
        echo "</pre>";

        echo "<h3>2. Data Preview (Target Sale ID: $targetSaleId)</h3>";
        echo "<pre style='background:#222; padding:20px; border-radius:8px; border:1px solid #555; color:#fff;'>";
        $sale = Sale::where('user_id', $targetUserId)->where('id', $targetSaleId)->first();
        if ($sale) {
            echo "Sale Found! Invoice: " . $sale->invoice_number . "\n";
            echo "Items Count: " . $sale->saleDetails()->count();
        } else {
            echo "❌ NO SALE FOUND locally for ID $targetSaleId under User $targetUserId.\n";
            echo "This means while you are logged in as " . $user->email . ", the database says Sale $targetSaleId belongs to a different ID.";
        }
        echo "</pre>";
    }
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
