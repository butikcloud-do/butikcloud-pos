<div class="table-filter">
    @if (request()->trash)
        <a href="{{ appendQuery('trash', false) }}" class="btn btn-outline--secondary">
            <span class="icon">
                <i class="las la-undo"></i>
            </span>
            @lang('Back to List')
        </a>
    @else
        <a href="{{ appendQuery('trash', true) }}" class="btn btn-outline--secondary">
            <span class="icon">
                <i class="las la-recycle"></i>
            </span>
            @lang('Recycle Bin')
        </a>
    @endif
</div>
