<div class="col-lg-8 col-sm-12">
    <div class="border-inline rounded overflow-hidden">


        <x-panel.ui.table>
            <x-panel.ui.table.header>
                <tr>
                    <th>@lang('Short Code')</th>
                    <th>@lang('Description')</th>
                </tr>
            </x-panel.ui.table.header>
            <x-panel.ui.table.body>
                @foreach ($template->shortcodes as $shortcode => $key)
                    <tr>
                        {{-- blade-formatter-disable --}}
                <td>
                    <span class="copyBtn cursor-pointer hover-change-text" data-copy="@php echo "{{". $shortcode ."}}"  @endphp">
                        <span class="text">@php echo "{{". $shortcode ."}}"  @endphp</span>
                        <span class="hover-text">@lang('Copy')</span>
                    </span>
                </td>
                {{-- blade-formatter-enable --}}
                        <td>{{ __($key) }}</td>
                    </tr>
                @endforeach
                @foreach (gs('global_shortcodes') as $shortCode => $codeDetails)
                    <tr>
                        {{-- blade-formatter-disable --}}
                <td>
                    <span class="copyBtn cursor-pointer  hover-change-text" data-copy="@{{@php echo $shortCode @endphp}}">
                        <span class="text">@{{@php echo $shortCode @endphp}}</span>
                        <span class="hover-text">@lang('Copy')</span>
                    </span>
                </td>
                {{-- blade-formatter-enable --}}
                        <td>{{ __($codeDetails) }}</td>
                    </tr>
                @endforeach
            </x-panel.ui.table.body>
        </x-panel.ui.table>
    </div>
</div>
