<?php

namespace App\Traits;

use App\Models\ProductStock;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait StockTransferOperation
{
    private function invoiceNumber()
    {
        $parentUser = getParentUser();
        $prefix     = gs('prefix_setting', $parentUser->id);
        $count      = StockTransfer::where('user_id', $parentUser->id)->count() + 1;
        $summationNumber = '1000';

        if ($prefix) {
            return $prefix->stock_transfer_invoice_prefix . ($summationNumber + $count);
        } else {
            return $summationNumber + $count;
        }
    }

    public function add()
    {
        $pageTitle  = "Stock Transfer";
        $view       = "Template::user.stock_transfer.add";
        $user       = getParentUser();
        $staffIds   = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $warehouses = Warehouse::where('user_id', $user->id)->active()->get();

        return responseManager("add_stock_transfer", $pageTitle, 'success', compact('pageTitle', 'view', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfer_date'                      => 'required|date',
            'warehouse_id'                       => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'                    => 'required|exists:warehouses,id',
            'stock_transfer'                     => 'required|array|min:1',
            "stock_transfer.*.product_id"        => "required|exists:product_stocks,product_id",
            "stock_transfer.*.product_detail_id" => "nullable|exists:product_stocks,product_details_id",
            "stock_transfer.*.quantity"          => "required|numeric|gt:0",
        ], [
            'stock_transfer.required'           => "At least one product is required for transfer.",
            'stock_transfer.min'                => "At least one product is required for transfer.",
            'stock_transfer.*.quantity.numeric' => "Quantity must be a valid number.",
            'stock_transfer.*.quantity.gt'      => "Quantity must be greater than 0.",
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        foreach ($request->stock_transfer as $value) {
            $sourceStock = ProductStock::where('product_id', $value['product_id'])
                ->where('product_details_id', $value['product_details_id'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();

            if (!$sourceStock || $sourceStock->stock < $value['quantity']) {
                throw new \Exception('Product is not available in this warehouse');
            }
        }

        $user       = getParentUser();

        try {
            DB::beginTransaction();

            $transfer                  = new StockTransfer();
            $transfer->invoice_number  = $this->invoiceNumber();
            $transfer->transfer_date   = $request->transfer_date;
            $transfer->warehouse_id    = $request->warehouse_id;
            $transfer->to_warehouse_id = $request->to_warehouse_id;
            $transfer->status          = $request->status;
            $transfer->reference_no    = $request->reference_no;
            $transfer->user_id         = $user->id;
            $transfer->added_by        = auth()->id();

            if ($request->hasFile('attachment')) {
                try {
                    $transfer->attachment = fileUploader($request->attachment, getFilePath("stock_transfer_attachment"));
                } catch (\Exception $exp) {
                    $notify[] = 'Couldn\'t upload your attachment';
                    return jsonResponse('exception', 'error', $notify);
                }
            }
            $transfer->save();

            foreach ($request->stock_transfer as $value) {
                $transferDetail                     = new StockTransferDetail();
                $transferDetail->stock_transfer_id  = $transfer->id;
                $transferDetail->product_id         = $value['product_id'];
                $transferDetail->product_details_id = $value['product_details_id'];
                $transferDetail->quantity           = $value['quantity'];
                $transferDetail->save();

                $sourceStock = ProductStock::where('product_id', $value['product_id'])
                    ->where('product_details_id', $value['product_details_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                $sourceStock->stock -= $value['quantity'];
                $sourceStock->save();

                $destinationStock = ProductStock::where('product_id', $value['product_id'])
                    ->where('product_details_id', $value['product_details_id'])
                    ->where('warehouse_id', $request->to_warehouse_id)
                    ->first();

                if (!$destinationStock) {
                    $destinationStock                     = new ProductStock();
                    $destinationStock->product_id         = $value['product_id'];
                    $destinationStock->product_details_id = $value['product_details_id'];
                    $destinationStock->warehouse_id       = $request->to_warehouse_id;
                    $destinationStock->stock              = 0;
                }

                $destinationStock->stock += $value['quantity'];
                $destinationStock->save();
            }
            DB::commit();
            $message[] = 'Stock transfer successfully';
            return jsonResponse('stock_transfer', 'success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            return jsonResponse('exception', 'error', $notify);
        }
    }

    public function list()
    {
        $pageTitle = "Stock Transfer List";
        $view      = "Template::user.stock_transfer.list";
        $user      = getParentUser();

        $transfers = StockTransfer::where('user_id', $user->id)->with(['toWarehouse', 'fromWarehouse'])
            ->searchable(['reference_no', 'invoice_number'])
            ->withCount('stockTransferDetails as total_items')
            ->orderBy('id', getOrderBy())
            ->paginate(getPaginate());

        return responseManager("stock_transfers", $pageTitle, 'success', compact('transfers', 'view', 'pageTitle'));
    }

    public function edit($id)
    {
        $pageTitle = "Edit Stock Transfer";
        $transfer  = StockTransfer::where("id", $id)->where("user_id", getParentUser()->id)
            ->with(['toWarehouse', 'fromWarehouse', 'stockTransferDetails', 'stockTransferDetails.product'])
            ->firstOrFailWithApi("stock_transfer");
        $view       = "Template::user.stock_transfer.edit";
        $warehouses = Warehouse::active()->get();

        return responseManager("edit_stock_transfer", $pageTitle, 'success', compact('pageTitle', 'view', 'transfer', 'warehouses'));
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'transfer_date'                      => 'required|date',
            'warehouse_id'                       => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'                    => 'required|exists:warehouses,id',
            'stock_transfer'                     => 'required|array|min:1',
            "stock_transfer.*.product_id"        => "required|exists:product_stocks,product_id",
            "stock_transfer.*.product_detail_id" => "nullable|exists:product_stocks,product_details_id",
            "stock_transfer.*.quantity"          => "required|numeric|gt:0",
        ], [
            'stock_transfer.required'           => "At least one product is required for transfer.",
            'stock_transfer.min'                => "At least one product is required for transfer.",
            'stock_transfer.*.quantity.numeric' => "Quantity must be a valid number.",
            'stock_transfer.*.quantity.gt'      => "Quantity must be greater than 0.",
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = getParentUser();

        try {
            DB::beginTransaction();

            $transfer = StockTransfer::where('user_id', $user->id)->find($id);

            if (!$transfer) {
                return responseManager('not_found', 'Stock transfer not found');
            }

            $oldTransferDetails = $transfer->stockTransferDetails;


            foreach ($oldTransferDetails as $detail) {

                $sourceStock = ProductStock::where('product_id', $detail->product_id)
                    ->where('product_details_id', $detail->product_details_id)
                    ->where('warehouse_id', $transfer->warehouse_id)
                    ->first();

                $destinationStock = ProductStock::where('product_id', $detail->product_id)
                    ->where('product_details_id', $detail->product_details_id)
                    ->where('warehouse_id', $transfer->to_warehouse_id)
                    ->first();

                if ($sourceStock) {
                    $sourceStock->stock += $detail->quantity;
                    $sourceStock->save();
                }

                if ($destinationStock) {
                    $destinationStock->stock -= $detail->quantity;
                    $destinationStock->save();
                }
            }

            $existingProductDetailsIds = array_column($request->stock_transfer, 'product_details_id');
            StockTransferDetail::where('stock_transfer_id', $transfer->id)
                ->whereNotIn('product_details_id', $existingProductDetailsIds)
                ->delete();

            $transfer->transfer_date   = $request->transfer_date;
            $transfer->warehouse_id    = $request->warehouse_id;
            $transfer->to_warehouse_id = $request->to_warehouse_id;
            $transfer->status          = $request->status;
            $transfer->reference_no    = $request->reference_no;

            if ($request->hasFile('attachment')) {
                $transfer->attachment = fileUploader($request->attachment, getFilePath("stock_transfer_attachment"));
            }

            $transfer->save();

            foreach ($request->stock_transfer as $value) {
                $sourceStock = ProductStock::where('product_id', $value['product_id'])
                    ->where('product_details_id', $value['product_details_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if (!$sourceStock || $sourceStock->stock < $value['quantity']) {
                    throw new \Exception('Product is not available in the from warehouse.');
                }

                $sourceStock->stock -= $value['quantity'];
                $sourceStock->save();

                $destinationStock = ProductStock::where('product_id', $value['product_id'])
                    ->where('product_details_id', $value['product_details_id'])
                    ->where('warehouse_id', $request->to_warehouse_id)
                    ->first();

                if (!$destinationStock) {
                    $destinationStock                     = new ProductStock();
                    $destinationStock->product_id         = $value['product_id'];
                    $destinationStock->product_details_id = $value['product_details_id'];
                    $destinationStock->warehouse_id       = $request->to_warehouse_id;
                    $destinationStock->stock              = 0;
                }

                $destinationStock->stock += $value['quantity'];
                $destinationStock->save();

                $transferDetail = StockTransferDetail::where('stock_transfer_id', $transfer->id)
                    ->where('product_id', $value['product_id'])
                    ->where('product_details_id', $value['product_details_id'])
                    ->first();

                if ($transferDetail) {
                    $transferDetail->quantity = $value['quantity'];
                } else {
                    $transferDetail                     = new StockTransferDetail();
                    $transferDetail->stock_transfer_id  = $transfer->id;
                    $transferDetail->product_id         = $value['product_id'];
                    $transferDetail->product_details_id = $value['product_details_id'];
                    $transferDetail->quantity           = $value['quantity'];
                }

                $transferDetail->save();
            }

            DB::commit();
            $message[] = 'Stock transfer updated successfully.';
            return jsonResponse('stock_transfer_update', 'success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            return jsonResponse('exception', 'error', $notify);
        }
    }

    public function view($id)
    {
        $pageTitle = "Stock Transfer Invoice";
        $view      = "Template::user.stock_transfer.view";
        $transfer  = StockTransfer::where("id", $id)
            ->where("user_id", getParentUser()->id)
            ->with(['toWarehouse', 'fromWarehouse', 'stockTransferDetails'])
            ->firstOrFailWithApi("stock_transfer");

        return responseManager("view_stock_transfer", $pageTitle, 'success', compact('transfer', 'view', 'pageTitle'));
    }

    public function pdf($id)
    {
        $pageTitle = "Stock Transfer Invoice";
        $transfer  = StockTransfer::where("id", $id)
            ->where("user_id", getParentUser()->id)
            ->with(['toWarehouse', 'fromWarehouse', 'stockTransferDetails'])
            ->firstOrFailWithApi("stock_transfer");

        $pdf      = Pdf::loadView('Template::user.stock_transfer.pdf', compact('transfer', 'pageTitle'));
        $fileName = "Stock Transfer Invoice - " . $transfer->invoice_number . ".pdf";

        return $pdf->stream($fileName);
    }
}
