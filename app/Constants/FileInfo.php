<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
     */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw',
        ];
        $data['depositVerify'] = [
            'path' => 'assets/images/verify/deposit',
        ];
        $data['expense_attachment'] = [
            'path' => 'assets/file/expense_attachment',
        ];
        $data['purchase_attachment'] = [
            'path' => 'assets/file/purchase_attachment',
        ];
        $data['stock_transfer_attachment'] = [
            'path' => 'assets/file/stock_transfer_attachment',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ];
        $data['verify'] = [
            'path' => 'assets/verify',
        ];
        $data['default'] = [
            'path' => 'assets/images/default.png',
        ];
        $data['ticket'] = [
            'path' => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path' => 'assets/images/logo_icon',
        ];
        $data['favicon'] = [
            'size' => '128x128',
        ];
        $data['extensions'] = [
            'path' => 'assets/images/extensions',
            'size' => '36x36',
        ];
        $data['seo'] = [
            'path' => 'assets/images/seo',
            'size' => '1180x600',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ];
        $data['adminProfile'] = [
            'path' => 'assets/ovopanel/images/profile',
            'size' => '400x400',
        ];
        $data['push'] = [
            'path' => 'assets/images/push_notification',
        ];
        $data['appPurchase'] = [
            'path' => 'assets/in_app_purchase_config',
        ];
        $data['maintenance'] = [
            'path' => 'assets/images/maintenance',
            'size' => '600x600',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '80x80',
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => '',
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => '',
        ];
        $data['pushConfig'] = [
            'path' => 'assets/admin',
        ];
        $data['product'] = [
            'path' => 'assets/images/product',
        ];
        $data['category'] = [
            'path' => 'assets/images/category',
            'size' => '200x200',
        ];
        $data['brand'] = [
            'path' => 'assets/images/brand',
            'size' => '200x200',
        ];
        $data['leaveAttachment'] = [
            'path' => 'assets/images/leave_attachment',
        ];

        $data['employeeImage'] = [
            'path' => 'assets/images/employee_image',
            'size' => '350x300',
        ];

        $data['employeeAttachment'] = [
            'path' => 'assets/images/employee_attachment',
        ];

        return $data;
    }
}
