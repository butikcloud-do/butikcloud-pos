@forelse($lowStockProducts as $lowStockProduct)
    <tr>
        <td>
            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-start">
                <span class="table-thumb">
                    <img src="{{ @$lowStockProduct->product->image_src }}">
                </span>
                <div>
                    <strong class="d-block text-start">
                        {{ strLimit(__(@$lowStockProduct->product->name), 10) }}
                        @if (@$lowStockProduct->product->product_type == Status::PRODUCT_TYPE_VARIABLE)
                            <span>
                                - {{ __(@$lowStockProduct->attribute->name) }}
                                - {{ __(@$lowStockProduct->variant->name) }}
                            </span>
                        @endif
                        <span class="fw-bold d-block">
                            {{ @$lowStockProduct->sku }}
                        </span>
                    </strong>
                </div>
            </div>
        </td>
        <td>
            <span class="badge badge--danger">
                {{ $lowStockProduct->product_stock_sum_stock }}
                {{ __(@$lowStockProduct->product->unit->short_name) }}
            </span>
        </td>
    </tr>
@empty
    <x-panel.ui.table.empty_message />
@endforelse
