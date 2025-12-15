<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('transactions/history', 'transactionsHistory')->name('transactions.history');


                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                Route::get('chart/sales-purchase', 'saleAndPurchaseChart')->name('chart.sales.purchase');
                Route::get('low-stock-product', 'lowStockProduct')->name('low.stock.product');

                //Report Bugs
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                Route::get('list', 'list')->name('list');
                Route::post('store', 'save')->name('store');
                Route::post('update/{id}', 'save')->name('update');
                Route::post('status-change/{id}', 'status')->name('status.change');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            Route::controller('GeneralSettingController')->group(function () {
                Route::get('general-setting', 'general')->name('setting.general')->middleware('staff.permission:general setting');
                Route::post('general-setting', 'generalUpdate')->middleware('staff.permission:general setting');

                // Invoice Setting
                Route::get('invoice-setting', 'invoiceSetting')->name('setting.invoice')->middleware('staff.permission:invoice setting');
                Route::post('invoice-setting', 'invoiceSettingUpdate')->name('setting.invoice.update')->middleware('staff.permission:invoice setting');

                //Company Setting
                Route::get('company-setting', 'companySetting')->name('setting.company')->middleware('staff.permission:company setting');
                Route::post('company-setting', 'companySettingUpdate')->name('setting.company.update')->middleware('staff.permission:company setting');

                Route::get('setting/brand', 'logoIcon')->name('setting.brand')->middleware('staff.permission:brand setting');
                Route::post('setting/brand', 'logoIconUpdate')->name('setting.brand')->middleware('staff.permission:brand setting');
            });

            Route::controller('PlanSubscribeController')->prefix('subscription')->name('subscription.')->group(function () {
                Route::get('index', 'index')->name('index');
                Route::get('invoice/{subscriptionId}', 'invoice')->name('invoice');
                Route::get('invoice/print/{subscriptionId}', 'printInvoice')->name('invoice.print');
                Route::get('invoice/download/{subscriptionId}', 'downloadInvoice')->name('invoice.download');
            });

            Route::controller('PlanSubscribeController')->prefix('subscription/plan')->name('subscription.')->group(function () {
                Route::get('index', 'index')->name('plan.index');
                Route::post('purchase/{id}', 'planPurchase')->name('plan.purchase');
            });

            //sale
            Route::controller('SaleController')->name('sale.')->prefix('sale')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view sale');

                Route::middleware('active.cash.register')->group(function () {
                    Route::get('add', 'add')->name('add')->middleware('staff.permission:add sale');
                    Route::post('store', 'store')->name('store')->middleware('staff.permission:add sale', 'has.subscription');
                    Route::get('edit/{id}', 'edit')->name('edit')->middleware('staff.permission:edit sale');
                    Route::post('update/{id}', 'update')->name('update')->middleware('staff.permission:edit sale', 'has.subscription');
                });

                Route::get('view/{id}', 'view')->name('view')->middleware('staff.permission:view sale');
                Route::get('pdf/{id}', 'pdf')->name('pdf')->middleware('staff.permission:download sale invoice');
                Route::get('print/{id}', 'print')->name('print')->middleware('staff.permission:print sale invoice');
                Route::post('remove/single/item/{id}', 'removeSingleItem')->name('remove.single.item');
                Route::post('apply/coupon', 'applyCoupon')->name('apply.coupon');
                Route::get('top-selling-product', 'topSellingProduct')->name('top.selling.product');
            });

            //purchase
            Route::controller('PurchaseController')->name('purchase.')->prefix('purchase')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view purchase');
                Route::middleware('active.cash.register')->group(function () {
                    Route::get('add', 'add')->name('add')->middleware('staff.permission:add purchase');
                    Route::post('store', 'store')->name('store')->middleware('staff.permission:add purchase', 'has.subscription');
                    Route::get('edit/{id}', 'edit')->name('edit')->middleware('staff.permission:edit purchase');
                    Route::post('update/{id}', 'update')->name('update')->middleware('staff.permission:edit purchase', 'has.subscription');
                    Route::post('ad/payment/{id}', 'addPayment')->name('ad.payment')->middleware('staff.permission:add purchase payment', 'active.cash.register');
                });
                Route::get('view/{id}', 'view')->name('view')->middleware('staff.permission:view purchase');
                Route::get('pdf/{id}', 'pdf')->name('pdf')->middleware('staff.permission:download purchase invoice');
                Route::get('print/{id}', 'print')->name('print');
                Route::post('update-status/{id}', 'updateStatus')->name('update.status')->middleware('staff.permission:update purchase status');
                Route::post('remove/single/item/{id}', 'removeSingleItem')->name('remove.single.item');
            });

            // Manage agent
            Route::controller('ManageStaffController')->prefix('staff')->name('staff.')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view staff');
                Route::get('create', 'create')->name('create')->middleware('staff.permission:view staff');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('staff.permission:view staff');
                Route::post('save', 'save')->name('save')->middleware('staff.permission:update staff');
                Route::post('update/{id}', 'update')->name('update')->middleware('staff.permission:update staff');
                Route::post('delete/{id}', 'delete')->name('delete')->middleware('staff.permission:update staff');
                Route::get('permissions/{id}', 'permissions')->name('permissions')->middleware('staff.permission:update staff');
                Route::post('permissions/save/{id}', 'updatePermissions')->name('permissions.update')->middleware('staff.permission:update staff');

                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash staff');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //product
            Route::controller('ProductController')->name('product.')->prefix('product')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view product');
                Route::get('create', 'create')->name('create')->middleware('staff.permission:add product');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add product', 'has.subscription');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('staff.permission:edit product');
                Route::get('view/{id}', 'view')->name('view')->middleware('staff.permission:view product');
                Route::post('update/{id}', 'update')->name('update')->middleware('staff.permission:edit product', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit product');
                Route::get('product-code-generate', 'generateProductCode')->name('code.generate');
                Route::get('print-label', 'printLabel')->name('print.label')->middleware('staff.permission:print product barcode');
                Route::get('search', 'search')->name('search');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash product');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            // Stock Transfer

            Route::controller('StockTransferController')->name('stock.transfer.')->prefix('stock-transfer')->middleware("staff.permission:view stock transfer")->group(function () {
                Route::get('add', 'add')->name('add')->middleware("staff.permission:add stock transfer");
                Route::get('list', 'list')->name('list');
                Route::post('store', 'store')->name('store')->middleware("staff.permission:add stock transfer", 'has.subscription');
                Route::get('view/{id}', 'view')->name('view')->middleware("staff.permission:view stock transfer");
                Route::get('edit/{id}', 'edit')->name('edit')->middleware("staff.permission:edit stock transfer");
                Route::post('update/{id}', 'update')->name('update')->middleware("staff.permission:edit stock transfer", 'has.subscription');
                Route::get('pdf/{id}', 'pdf')->name('pdf');
                Route::get('print/{id}', 'print')->name('print');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
            });
            Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
                Route::get('invoice-wise', 'invoiceWiseReport')->name('profit.invoice_wise')->middleware('staff.permission:view profit loss report');
                Route::get('product-wise', 'productWiseReport')->name('profit.product_wise')->middleware('staff.permission:view profit loss report');
                Route::get('sale', 'saleReport')->name('sale')->middleware('staff.permission:view sale report');
                Route::get('purchase', 'purchaseReport')->name('purchase')->middleware('staff.permission:view purchase report');
                Route::get('stock', 'stockReport')->name('stock')->middleware('staff.permission:view stock report');
                Route::get('expense', 'expenseReport')->name('expense')->middleware('staff.permission:view expense report');
                Route::get('notification/history', 'notificationHistory')->name('notification.history')->middleware('staff.permission:notification setting');
                Route::get('email/detail/{id}', 'emailDetails')->name('email.details')->middleware('staff.permission:notification setting');
                Route::get('transaction', 'transaction')->name('transaction');
                Route::get('login/history', 'loginHistory')->name('login.history');
                Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
                Route::get('notification/history', 'notificationHistory')->name('notification.history');
                Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
            });

            //customer
            Route::controller('CustomerController')->name('customer.')->prefix('customer')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view customer');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add customer', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit customer', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit customer');
                Route::get('lazy-loading', 'lazyLoadingData')->name('lazy.loading');
                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash customer');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //expense
            Route::name('expense.')->prefix('expense')->group(function () {
                //expense category
                Route::controller('ExpenseCategoryController')->name('category.')->prefix("category")->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view expense category');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add expense category', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit expense category', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit expense category');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash expense category');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                //expense
                Route::controller('ExpenseController')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view expense');

                    Route::middleware('active.cash.register', 'has.subscription')->group(function () {
                        Route::post('create', 'save')->name('create')->middleware('staff.permission:add expense');
                        Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit expense');
                    });
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit expense');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash expense');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });
            });

            //Payment type
            Route::controller('PaymentTypeController')->name('payment.type.')->prefix('payment-type')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view payment type');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add payment type', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit payment type', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit payment type');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash payment type');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });



            //warehouse
            Route::controller('WareHoseController')->name('warehouse.')->prefix('warehouse')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view warehouse');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add warehouse', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit warehouse', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit warehouse');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash warehouse');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //category
            Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view category');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add category', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit category', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit category');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash category');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //brand
            Route::controller('BrandController')->name('brand.')->prefix('brand')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view brand');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add brand', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit brand', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit brand');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash brand');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //unit
            Route::controller('UnitController')->name('unit.')->prefix('unit')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view unit');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add unit', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit unit', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit unit');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash unit');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //attribute
            Route::controller('AttributeController')->name('attribute.')->prefix('attribute')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view attribute');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add attribute', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit attribute', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit attribute');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash attribute');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //variant
            Route::controller('VariantController')->name('variant.')->prefix('variant')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view variant');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add variant', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit variant', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit variant');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash variant');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //tax
            Route::controller('TaxController')->name('tax.')->prefix('tax')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view tax');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add tax', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit tax', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit tax');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash tax');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //coupon
            Route::controller('CouponController')->name('coupon.')->prefix('coupon')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view coupon');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add coupon', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit coupon', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit coupon');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash coupon');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //customer
            Route::controller('CustomerController')->name('customer.')->prefix('customer')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view customer');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add customer', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit customer', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit customer');
                Route::get('lazy-loading', 'lazyLoadingData')->name('lazy.loading');
                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash customer');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });

            //supplier
            Route::controller('SupplierController')->name('supplier.')->prefix('supplier')->group(function () {
                Route::get('list', 'list')->name('list')->middleware('staff.permission:view supplier');
                Route::get('view/{id}', 'view')->name('view')->middleware('staff.permission:view supplier');
                Route::post('create', 'save')->name('create')->middleware('staff.permission:add supplier', 'has.subscription');
                Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit supplier', 'has.subscription');
                Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit supplier');
                Route::get('lazy-loading', 'lazyLoadingData')->name('lazy.loading');
                Route::post('add-payment/{id}', 'addPayment')->name('add.payment')->middleware('staff.permission:add purchase payment');

                //trash
                Route::prefix('trash')->name('trash.')->group(function () {
                    Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash supplier');
                    Route::post('restore/{id}', 'restoreTrash')->name('restore');
                    Route::get('list', 'listTrash')->name('list');
                });
            });


            // Company
            Route::middleware('has.hrm')->group(function () {
                Route::controller('CompanyController')->name('company.')->prefix('company')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view company');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add company', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit company', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit company');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash company');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Department
                Route::controller('DepartmentController')->name('department.')->prefix('department')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view department');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add department', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit department', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit department');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash department');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Designation
                Route::controller('DesignationController')->name('designation.')->prefix('designation')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view designation');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add designation', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit designation', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit designation');
                    Route::get('get-department/{companyId}', 'getDepartment')->name('get.departments');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash designation');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Shift
                Route::controller('ShiftController')->name('shift.')->prefix('shift')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view shift');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add shift', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit shift', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit shift');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash shift');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Employee
                Route::controller('EmployeeController')->name('employee.')->prefix('employee')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view employee');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add employee', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit employee', 'has.subscription');
                    Route::post('status-change/{id}', 'status')->name('status.change')->middleware('staff.permission:edit employee');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash employee');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Leave Request
                Route::controller('LeaveController')->name('leave.')->prefix('leave')->group(function () {
                    Route::get('request/list', 'list')->name('request.list')->middleware('staff.permission:view leave request');
                    Route::post('request/create', 'save')->name('request.create')->middleware('staff.permission:add leave request', 'has.subscription');
                    Route::post('request/update/{id}', 'save')->name('request.update')->middleware('staff.permission:edit leave request', 'has.subscription');

                    //Request trash
                    Route::prefix('request/trash')->name('request.trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash leave request');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });

                    Route::get('type/list', 'typeList')->name('type.list')->middleware('staff.permission:view leave type');
                    Route::post('type/create', 'typeSave')->name('type.create')->middleware('staff.permission:add leave type', 'has.subscription');
                    Route::post('type/update/{id}', 'typeSave')->name('type.update')->middleware('staff.permission:edit leave type', 'has.subscription');
                    Route::post('type-status-change/{id}', 'typeStatus')->name('type.status.change')->middleware('staff.permission:edit leave type');

                    //Type trash
                    Route::prefix('type/trash')->name('type.trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash leave type');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Attendance
                Route::controller('AttendanceController')->name('attendance.')->prefix('attendance')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view attendance', 'has.subscription');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add attendance', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit attendance');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash attendance');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Holidays
                Route::controller('HolidayController')->name('holiday.')->prefix('holiday')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view holiday');
                    Route::post('create', 'save')->name('create')->middleware('staff.permission:add holiday', 'has.subscription');
                    Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit holiday', 'has.subscription');

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash holiday');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });

                // Payroll
                Route::controller('PayrollController')->name('payroll.')->prefix('payroll')->group(function () {
                    Route::get('list', 'list')->name('list')->middleware('staff.permission:view payroll');
                    Route::middleware('active.cash.register')->group(function () {
                        Route::post('create', 'save')->name('create')->middleware('staff.permission:add payroll', 'has.subscription');
                        Route::post('update/{id}', 'save')->name('update')->middleware('staff.permission:edit payroll', 'has.subscription');
                    });

                    //trash
                    Route::prefix('trash')->name('trash.')->group(function () {
                        Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('staff.permission:trash payroll');
                        Route::post('restore/{id}', 'restoreTrash')->name('restore');
                        Route::get('list', 'listTrash')->name('list');
                    });
                });
            });

            // cash register
            Route::controller('CashRegisterController')->name('cash_register.')->prefix('cash-register')->group(function () {
                Route::get('list', 'list')->name('list');
                Route::get('dashboard', 'dashboard')->name('dashboard');
                Route::post('store', 'store')->name('store');
                Route::post('close', 'close')->name('close');
                Route::post('store/transaction', 'storeTransaction')->name('store.transaction');
                Route::get('report', 'report')->name('report');
                Route::get('report/{id}', 'reportDetails')->name('report.details');
                Route::get('account-wise-details/{cashRegisterId}/{accountId}/{type}', 'cashRegisterAccountWiseDetails')->name('account.wise.details');
            });


            Route::name('pos.')->prefix('pos')->middleware("active.cash.register")->group(function () {
                Route::controller('Pos\PosController')->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::get('category', 'category')->name('category');
                    Route::get('brand', 'brand')->name('brand');
                    Route::get('product', 'product')->name('product');
                    Route::get('pricing-details/{id}', 'productPricingDetails')->name('product.pricing.details');
                });
            });
        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
