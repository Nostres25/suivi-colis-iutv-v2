<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\CustomAdminAccess;
use Database\Seeders\PermissionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

// orders get
Route::get('/', [OrderController::class, 'viewOrders']);
Route::get('/orders', [OrderController::class, 'viewOrders'])->name('orders');
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
Route::get('/order/{id}/step-actions/upload-purchase-order', [OrderController::class, 'modalUploadPurchaseOrder'])
    ->name('orders.step-actions.upload-purchase-order');
Route::get('/order/{id}/step-actions/refuse', [OrderController::class, 'modalRefuse'])
    ->name('orders.step-actions.refuse');
Route::get('/order/{id}/step-actions/paid', [OrderController::class, 'modalPaid'])
    ->name('orders.step-actions.paid');
Route::get('/order/{id}/step-actions/upload-delivery-note', [OrderController::class, 'modalUploadDeliveryNote'])
    ->name('orders.step-actions.upload-delivery-note');
Route::get('/order/{id}/step-actions/sent-to-supplier', [OrderController::class, 'modalSentToSupplier'])
    ->name('orders.step-actions.sent-to-supplier');
Route::get('/order/{id}/step-actions/packaged-delivered', [OrderController::class, 'modalDeliveredPackages'])
    ->name('orders.step-actions.packages-delivered');
Route::get('/order/{id}/step-actions/all-delivered', [OrderController::class, 'modalDeliveredAll'])
    ->name('orders.step-actions.all-delivered');
Route::get('/order/{id}/step-actions/supplier-response-package-infos', [OrderController::class, 'modalSupplierReponseInfosPackages'])
    ->name('orders.step-actions.package-infos');
Route::get('/order/{id}/view-details', [OrderController::class, 'modalViewDetails'])
    ->name('orders.modal.view-details');

// orders post modals post

Route::post('/order/{id}/step-actions/upload-purchase-order', [OrderController::class, 'actionUploadPurchaseOrder'])
    ->name('orders.step-actions.upload-purchase-order');
Route::post('/order/{id}/step-actions/paid', [OrderController::class, 'actionOrderPaid'])
    ->name('orders.step-actions.paid');

// EDIT ORDER
Route::post('/order/{id}/view-details', [OrderController::class, 'editOrder'])
    ->name('orders.modal.view-details');

Route::post('/orders/create', [OrderController::class, 'submitNewOrder'])
    ->name('orders.create');

// Routes de téléchargement
Route::get('/order/{id}/document/{type}', [OrderController::class, 'downloadDocument'])
    ->name('orders.download');

// suppliers modals get
Route::get('/supplier/{id}/view-details', [SupplierController::class, 'modalViewDetails'])
    ->name('suppliers.modal.view-details');

// suppliers modals post
Route::post('/supplier/{id}/view-details', [SupplierController::class, 'editSupplier'])
    ->name('suppliers.modal.view-details');

// seulement pour les tests sur le serveur de l'IUT
Route::get('/cookies', function (Request $request) {
    dd($request->cookie());
});

// logout

Route::get('/logout', function (Request $request) {

    if (config('app.debug')) {
        error_log('cookies avant déconnexion : '.implode(', ', array_keys($request->cookie())));
    }
    info($request->cookie());
    // Cookies à supprimer pour se déconnecter² et rediriger automatiquement vers le CAS avec apache2
    Cookie::queue(Cookie::forget('MOD_AUTH_CAS'));
    Cookie::queue(Cookie::forget('MOD_AUTH_CAS_S'));

    // Déconnecter l'utilisateur
    Auth::logout();

    info($request->cookie());
    if (config('app.debug')) {
        error_log('cookies après déconnexion : '.implode(', ', array_keys($request->cookie())));
    }

    return back();

});

// Affiche la page de profil et permet de modifier les informations de l'utilisateur
Route::get('/account/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/account/profile', [ProfileController::class, 'update'])->name('profile.update');

// Page "À propos"
Route::get('/about', [AboutController::class, 'about']);

// Refuser devis POST

Route::post('/order/{id}/step-actions/refuse', [OrderController::class, 'actionRefuse'])
    ->name('orders.step-actions.refuse');

// Commande commandé POST
Route::post('/order/{id}/step-actions/sent-to-supplier', [OrderController::class, 'actionSentToSupplier'])
    ->name('orders.step-actions.sent-to-supplier');

// Information colis POST
Route::post(
    '/order/{id}/step-actions/supplier-response-package-infos',
    [OrderController::class, 'actionUpdatePackageInfos']
)->name('orders.step-actions.package-infos');

Route::get(
    '/order/{id}/step-actions/upload-delivery-note',
    [OrderController::class, 'modalUploadDeliveryNote']
)->name('orders.step-actions.upload-delivery-note');

// Fonctionnement adminer + restrictions d'accès
Route::any('/adminer', function () {
    $adminerDir = base_path('vendor/vrana/adminer/adminer');

    if (is_dir($adminerDir)) {
        chdir($adminerDir);
        require 'index.php';

        return '';
    }

    return "Erreur : Le paquet Composer 'vrana/adminer' n'est pas trouvé. Avez-vous fait 'composer require vrana/adminer' ?";

})->where('any', '.*')
    ->middleware([CustomAdminAccess::class]);
