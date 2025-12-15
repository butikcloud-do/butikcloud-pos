<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {

    Route::controller('AppController')->group(function () {
        Route::get('general-setting', 'generalSetting');
        Route::get('get-countries', 'getCountries');
        Route::get('language/{key}', 'getLanguage');
    });

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('login', 'login');
        });
        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::controller('UserController')->group(function () {

            Route::get('dashboard', "dashboard");
            Route::get('sales-data', "salesData");
            Route::get('recent-transactions', "recentTransactions");

            Route::get('profile', 'profile');
            Route::post('profile', 'profileUpdate');
            Route::post('password', 'passwordUpdate');


            Route::prefix('admin')->group(function () {
                Route::get('list', 'list')->middleware('staff.permission:view admin');
                Route::post('create', 'save')->middleware('staff.permission:add admin');
                Route::post('update/{id}', 'save')->middleware('staff.permission:edit admin');
            });
            Route::post('prefix-setting', 'prefixSettingUpdate')->middleware('staff.permission:prefix setting');
            Route::post('company-setting', 'companySettingUpdate')->middleware('staff.permission:company setting');
        });

        //customer
        Route::controller('CustomerController')->prefix('customer')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view customer');
            Route::post('create', 'save')->middleware('staff.permission:add customer');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit customer');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit customer');
        });

        //supplier
        Route::controller('SupplierController')->prefix('supplier')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view supplier');
            Route::get('view/{id}', 'view')->middleware('staff.permission:view supplier');
            Route::post('create', 'save')->middleware('staff.permission:add supplier');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit supplier', 'has.subscription');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit supplier');
            Route::post('add-payment/{id}', 'addPayment')->middleware('staff.permission:add purchase payment');
        });

        //category
        Route::controller('CategoryController')->prefix('category')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view category');
            Route::post('create', 'save')->middleware('staff.permission:add category');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit category');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit category');
        });

        //brand
        Route::controller('BrandController')->prefix('brand')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view brand');
            Route::post('create', 'save')->middleware('staff.permission:add brand');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit brand');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit brand');
        });

        //attribute
        Route::controller('AttributeController')->prefix('attribute')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view attribute');
            Route::post('create', 'save')->middleware('staff.permission:add attribute');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit attribute');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit attribute');
        });

        //variant
        Route::controller('VariantController')->prefix('variant')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view variant');
            Route::post('create', 'save')->middleware('staff.permission:add variant');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit variant');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit variant');
        });

        //tax
        Route::controller('TaxController')->prefix('tax')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view tax');
            Route::post('create', 'save')->middleware('staff.permission:add tax');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit tax');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit tax');
        });

        //expense
        Route::prefix('expense')->group(function () {
            //expense category
            Route::controller('ExpenseCategoryController')->prefix("category")->group(function () {
                Route::get('list', 'list')->middleware('staff.permission:view expense category');
                Route::post('create', 'save')->middleware('staff.permission:add expense category', 'has.subscription');
                Route::post('update/{id}', 'save')->middleware('staff.permission:edit expense category');
                Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit expense category');
            });

            //expense
            Route::controller('ExpenseController')->group(function () {
                Route::get('list', 'list')->middleware('staff.permission:view expense');
                Route::middleware("active.cash.register")->group(function () {
                    Route::post('create', 'save')->middleware('staff.permission:add expense', 'has.subscription');
                    Route::post('update/{id}', 'save')->middleware('staff.permission:edit expense');
                });

                Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit expense');
                Route::post('remove/{id}', 'remove')->middleware('staff.permission:trash expense');
            });
        });

        //role
        Route::controller('RoleController')->prefix('role')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view role');
            Route::post('create', 'save')->middleware('staff.permission:add role');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit role');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit role');
            Route::get('permission/{id}', 'permission')->middleware('staff.permission:assign permission');
            Route::post('permission/update/{id}', 'permissionUpdate')->middleware('staff.permission:assign permission');
            Route::post('assign/permission', 'assignPermission');
        });


        //coupon
        Route::controller('CouponController')->prefix('coupon')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view coupon');
            Route::post('create', 'save')->middleware('staff.permission:add coupon');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit coupon');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit coupon');
        });

        //warehouse
        Route::controller('WareHoseController')->prefix('warehouse')->group(function () {
            Route::get('list', 'list');
            Route::post('create', 'save')->middleware('staff.permission:add warehouse');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit warehouse');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit warehouse');
        });

        //Payment type
        Route::controller('PaymentTypeController')->prefix('payment-type')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view payment type');
            Route::post('create', 'save')->middleware('staff.permission:add payment type');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit payment type');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit payment type');
        });

        //product
        Route::controller('ProductController')->prefix('product')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view product');
            Route::get('create', 'create')->middleware('staff.permission:add product');
            Route::post('create', 'save')->middleware('staff.permission:add product');
            Route::get('edit/{id}', 'edit')->middleware('staff.permission:edit product');
            Route::post('update/{id}', 'update')->middleware('staff.permission:edit product');
            Route::get('view/{id}', 'view')->middleware('staff.permission:view product');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit product');
            Route::get('product-code-generate', 'generateProductCode');
            Route::get('search', 'search');
        });

        //unit
        Route::controller('UnitController')->prefix('unit')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view unit');
            Route::post('create', 'save')->middleware('staff.permission:add unit');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit unit');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit unit');
        });

        //purchase
        Route::controller('PurchaseController')->prefix('purchase')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view purchase');
            Route::get('create', 'add')->middleware('staff.permission:add purchase');
            Route::middleware("active.cash.register")->group(function () {
                Route::post('create', 'store')->middleware('staff.permission:add purchase');
                Route::post('update/{id}', 'update')->middleware('staff.permission:edit purchase');
                Route::post('add/payment/{id}', 'addPayment')->middleware('staff.permission:add purchase payment');
            });
            Route::get('edit/{id}', 'edit')->middleware('staff.permission:edit purchase');
            Route::get('view/{id}', 'view')->middleware('staff.permission:view purchase');
            Route::get('pdf/{id}', 'pdf')->middleware('staff.permission:download purchase invoice');
            Route::post('update-status/{id}', 'updateStatus')->middleware('staff.permission:update purchase status');
            Route::get('attachment/{id}', 'downloadAttachment');
        });

        // sales
        Route::controller('SalesController')->prefix('sales')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view sale');
            Route::get('create', 'add')->middleware('staff.permission:add sale');
            Route::get('edit/{id}', 'edit')->middleware('staff.permission:edit sale');
            Route::middleware("active.cash.register")->group(function () {
                Route::post('create', 'store')->middleware('staff.permission:add sale');
                Route::post('update/{id}', 'update')->middleware('staff.permission:edit sale');
            });
            Route::get('view/{id}', 'view')->middleware('staff.permission:view sale');
            Route::get('pdf/{id}', 'pdf')->middleware('staff.permission:download sale invoice');
            Route::get('product-list', 'productList')->middleware('staff.permission:view sale');
            Route::post('add-to-cart', 'addToCart')->middleware('staff.permission:view sale');
            Route::post('cart-qty-update/{cartId}', 'updateCartQuantity')->middleware('staff.permission:view sale');
            Route::post('cart-count', 'countCart');
            Route::get('cart-remove', 'removeCart')->middleware('staff.permission:view sale');
            Route::get('cart-remove/{id}', 'removeSingleCart')->middleware('staff.permission:view sale');
            Route::get('checkout', 'checkout')->middleware('staff.permission:view sale');
            Route::get('required-data', 'requiredData')->middleware('staff.permission:view sale');
            Route::get('coupon', 'coupon')->middleware('staff.permission:view sale');
            Route::post('coupon-apply', 'applyCoupon')->middleware('staff.permission:view sale');
            Route::get('payment-method', 'paymentMethod')->middleware('staff.permission:view sale');
        });

        // Report
        Route::controller('ReportController')->prefix('report')->group(function () {
            Route::get('invoice-wise', 'invoiceWiseReport')->middleware('staff.permission:view profit loss report');
            Route::get('product-wise', 'productWiseReport')->middleware('staff.permission:view profit loss report');
            Route::get('sale', 'saleReport')->middleware('staff.permission:view sale report');
            Route::get('purchase', 'purchaseReport')->middleware('staff.permission:view purchase report');
            Route::get('stock', 'stockReport')->middleware('staff.permission:view stock report');
            Route::get('expense', 'expenseReport')->middleware('staff.permission:view expense report');
            Route::get('notification/history', 'notificationHistory')->middleware('staff.permission:notification setting');
            Route::get('transaction', 'transaction')->name('transaction');
        });


        // HRM & Payroll

        // Company
        Route::controller('CompanyController')->prefix('company')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view company');
            Route::post('create', 'save')->middleware('staff.permission:add company');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit company');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit company');
        });

        // Department
        Route::controller('DepartmentController')->prefix('department')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view department');
            Route::post('create', 'save')->middleware('staff.permission:add department');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit department');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit department');
        });

        // Designation
        Route::controller('DesignationController')->prefix('designation')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view designation');
            Route::post('create', 'save')->middleware('staff.permission:add designation');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit designation');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit designation');
        });


        // Shift
        Route::controller('ShiftController')->prefix('shift')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view shift');
            Route::post('create', 'save')->middleware('staff.permission:add shift');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit shift');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit shift');
        });


        // Employee
        Route::controller('EmployeeController')->prefix('employee')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view employee');
            Route::post('create', 'save')->middleware('staff.permission:add employee');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit employee');
            Route::post('status-change/{id}', 'status')->middleware('staff.permission:edit employee');
        });


        // Attendance
        Route::controller('AttendanceController')->prefix('attendance')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view attendance');
            Route::post('create', 'save')->middleware('staff.permission:add attendance');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit attendance');
        });


        // Holidays
        Route::controller('HolidayController')->prefix('holiday')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view holiday');
            Route::post('create', 'save')->middleware('staff.permission:add holiday');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit holiday');
        });


        // Payroll
        Route::controller('PayrollController')->prefix('payroll')->group(function () {
            Route::get('list', 'list')->middleware('staff.permission:view payroll');
            Route::post('create', 'save')->middleware('staff.permission:add payroll');
            Route::post('update/{id}', 'save')->middleware('staff.permission:edit payroll');
        });


        // Leave Request
        Route::controller('LeaveController')->prefix('leave')->group(function () {
            Route::get('request/list', 'list')->middleware('staff.permission:view leave request');
            Route::post('request/create', 'save')->middleware('staff.permission:add leave request');
            Route::post('request/update/{id}', 'save')->middleware('staff.permission:edit leave request');

            Route::get('type/list', 'typeList')->middleware('staff.permission:view leave type');
            Route::post('type/create', 'typeSave')->middleware('staff.permission:add leave type');
            Route::post('type/update/{id}', 'typeSave')->middleware('staff.permission:edit leave type');
            Route::post('type-status-change/{id}', 'typeStatus')->middleware('staff.permission:edit leave type');
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });
});
