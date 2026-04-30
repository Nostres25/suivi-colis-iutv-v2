<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

// orders get
Route::get('/', [OrderController::class, 'viewOrders']);
Route::get('/orders', [OrderController::class, 'viewOrders']);
Route::get('/orders', [OrderController::class, 'viewOrders'])->name('orders.index');
Route::get('/orders/fetch/table', [OrderController::class, 'fetchOrdersTable'])
    ->name('orders.fetch.table');

// orders post
Route::post('/orders', [OrderController::class, 'submitNewOrder'])->name('orders.submitNewOrder');

// suppliers get
Route::get('suppliers', [SupplierController::class, 'viewSuppliers']);
Route::get('/suppliers/fetch/table', [SupplierController::class, 'fetchSuppliersTable'])
    ->name('suppliers.fetch.table');



// orders modals get
Route::get('/order/{id}/uploadPurchaseOrder', [OrderController::class, 'modalUploadPurchaseOrder'])
    ->name('orders.modal.uploadPurchaseOrder');
Route::get('/order/{id}/refuse', [OrderController::class, 'modalRefuse'])
    ->name('orders.modal.refuse');
Route::get('/order/{id}/paid', [OrderController::class, 'modalPaid'])
    ->name('orders.modal.paid');
Route::get('/order/{id}/uploadDeliveryNote', [OrderController::class, 'modalUploadDeliveryNote'])
    ->name('orders.modal.uploadDeliveryNote');
Route::get('/order/{id}/sentToSupplier', [OrderController::class, 'modalSentToSupplier'])
    ->name('orders.modal.sentToSupplier');
Route::get('/order/{id}/deliveredPackages', [OrderController::class, 'modalDeliveredPackages'])
    ->name('orders.modal.deliveredPackages');
Route::get('/order/{id}/deliveredAll', [OrderController::class, 'modalDeliveredAll'])
    ->name('orders.modal.deliveredAll');
Route::get('/order/{id}/viewDetails', [OrderController::class, 'modalViewDetails'])
    ->name('orders.modal.viewDetails');

// orders post modals post

Route::post('/order/{id}/uploadPurchaseOrder', [OrderController::class, 'actionUploadPurchaseOrder'])
    ->name('orders.uploadPurchaseOrder');

// EDIT ORDER
Route::post('/order/{id}/viewDetails', [OrderController::class, 'modalViewDetails'])
    ->name('orders.modal.viewDetails');

Route::post('/orders/create', [OrderController::class, 'submitNewOrder'])
    ->name('orders.create');

// Ajoutez cette route dans votre groupe de routes authentifiées
Route::get('/order/{id}/document/{type}', [OrderController::class, 'downloadDocument'])
    ->name('orders.download');


// suppliers modals get
Route::get('/supplier/{id}/viewDetails', [SupplierController::class, 'modalViewDetails'])
    ->name('suppliers.modal.viewDetails');



// suppliers modals post
Route::post('/supplier/{id}/viewDetails', [SupplierController::class, 'modalViewDetails'])
    ->name('suppliers.modal.viewDetails');


// Seulement pour les tests sur le serveur de l'IUT
Route::get('/cookies', function (Request $request) {
    dd($request->cookie());
});

// logout

Route::get('/logout', function (Request $request) {

    error_log('cookies avant déconnexion : '.implode(', ', array_keys($request->cookie())));
    info($request->cookie());
    // Cookies à supprimer pour se déconnecter² et rediriger automatiquement vers le CAS avec apache2
    Cookie::queue(Cookie::forget('MOD_AUTH_CAS'));
    Cookie::queue(Cookie::forget('MOD_AUTH_CAS_S'));

    // Déconnecter l'utilisateur
    Auth::logout();

    info($request->cookie());
    error_log('cookies après déconnexion : '.implode(', ', array_keys($request->cookie())));

    return back();

});

// Affiche la page de profil et permet de modifier les informations de l'utilisateur
Route::get('/account/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/account/profile', [ProfileController::class, 'update'])->name('profile.update');

// Page "À propos"
Route::get('/about', [AboutController::class, 'about']);


