<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Traits\ProductOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    use ProductOperation, RecycleBinManager;

    public function printLabel()
    {
        $pageTitle = "Print Label";
        return view('Template::user.product.label', compact('pageTitle'));
    }

    public function temporaryTrash($id)
    {
        try {
            DB::beginTransaction();

            $user = getParentUser();
            $product = \App\Models\Product::where('user_id', $user->id)->findOrFail($id);

            // Hard delete related product_details to free up SKUs
            ProductDetail::where('product_id', $product->id)->delete();

            // Soft delete the product
            $product->delete();

            DB::commit();

            $message = "Product trashed successfully";
            adminActivity("product-trash", get_class($product), $id);
            return responseManager('trash', $message, 'success');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Log comprehensive error details
            Log::error('Product Trash Failed', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'product_id' => $id ?? null,
                'user_id' => getParentUser()->id ?? null,
            ]);

            $message = "Failed to trash product: " . $e->getMessage();
            return responseManager('trash', $message, 'error');
        }
    }
}
