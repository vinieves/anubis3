<?php

use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\UpsellController;
use App\Http\Controllers\Checkout\Upsell2Controller;
use App\Http\Controllers\Pay\PayController;
use App\Http\Controllers\Pay\PayUpsellController;
use App\Http\Controllers\Pay\PayUpsell2Controller;
use Illuminate\Support\Facades\Route;

// ============================================
// CHECKOUT ANTIGO (Backup - não mexer)
// ============================================
Route::get('/checkout', [CheckoutController::class, 'index']);
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('checkout.createOrder');

Route::get('/upsell1', function () {
    return view('checkout.upsell1');
});

Route::post('/upsell1/process', [UpsellController::class, 'processUpsell'])->name('upsell1.process');

Route::get('/upsell2', function () {
    return view('checkout.upsell2');
});

Route::post('/upsell2/process', [Upsell2Controller::class, 'processUpsell2'])->name('upsell2.process');

Route::get('/thankyou', function () {
    return view('checkout.thankyou');
});

// ============================================
// NOVO SISTEMA /PAY (Com Facebook Pixel)
// ============================================
Route::prefix('pay')->group(function () {
    // Checkout principal
    Route::get('/', [PayController::class, 'index'])->name('pay.index');
    Route::post('/create-order', [PayController::class, 'createOrder'])->name('pay.createOrder');
    
    // Upsell 1
    Route::get('/upsell1', [PayUpsellController::class, 'index'])->name('pay.upsell1');
    Route::post('/upsell1/process', [PayUpsellController::class, 'processUpsell'])->name('pay.upsell1.process');
    
    // Upsell 2
    Route::get('/upsell2', [PayUpsell2Controller::class, 'index'])->name('pay.upsell2');
    Route::post('/upsell2/process', [PayUpsell2Controller::class, 'processUpsell2'])->name('pay.upsell2.process');
    
    // Thank you
    Route::get('/thankyou', function () {
        $pixelService = new \App\Services\FacebookPixelService('pay');

        return view('pay.thankyou', [
            'pixelId' => $pixelService->getPixelId(),
            'pixelEnabled' => $pixelService->isEnabled(),
            'conversionData' => session('pay_conversion_data'),
            'trackingData' => session('pay_tracking', []),
        ]);
    })->name('pay.thankyou');
});
