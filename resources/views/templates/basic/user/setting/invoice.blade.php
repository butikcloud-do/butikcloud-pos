@extends($activeTemplate . 'layouts.master')
@section('panel')
    <form method="POST" action="{{ route('user.setting.invoice.update') }}">
        <x-panel.ui.card>
            <x-panel.ui.card.body>
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Purchase Invoice Prefix')</label>
                            <input class="form-control" type="text" name="purchase_invoice_prefix" required value="{{ @$prefix->prefix_setting->purchase_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Sale Invoice Prefix')</label>
                            <input class="form-control" type="text" name="sale_invoice_prefix" required value="{{ @$prefix->prefix_setting->sale_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Product Code Prefix')</label>
                            <input class="form-control" type="text" name="product_code_prefix" required value="{{ @$prefix->prefix_setting->product_code_prefix }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> @lang('Stock Transfer Invoice Prefix')</label>
                            <input class="form-control" type="text" name="stock_transfer_invoice_prefix" required value="{{ @$prefix->prefix_setting->stock_transfer_invoice_prefix }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <x-panel.ui.btn.submit />
                    </div>
                </div>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </form>
@endsection