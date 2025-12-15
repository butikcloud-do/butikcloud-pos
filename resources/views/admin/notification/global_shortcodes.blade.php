<div class="col-lg-8">
    <div class="border-inline rounded overflow-hidden">
        <x-panel.ui.table>
            <x-panel.ui.table.header>
                <tr>
                    <th>@lang('Short Code') </th>
                    <th>@lang('Description')</th>
                </tr>
            </x-panel.ui.table.header>
            <x-panel.ui.table.body>
                {{-- blade-formatter-disable --}}
                <tr>
                    <td>
                        <span class="copyBtn hover-change-text cursor-pointe" data-copy="@{{fullname}}">
                            <span class="text">@{{fullname}}</span>
                            <span class="hover-text">@lang('Copy')</span>
                        </span>
                    </td>
                    <td>@lang('Full Name of User')</td>
                </tr>
                <tr>
                    <td>
                        <span class="copyBtn cursor-pointer hover-change-text" data-copy="@{{username}}">
                            <span class="text">@{{username}}</span>
                            <span class="hover-text">@lang('Copy')</span>
                        </span>
                    </td>
                    <td>@lang('Username of User')</td>
                </tr>
                <tr>
                    <td>
                        <span class="copyBtn cursor-pointer hover-change-text" data-copy="@{{message}}">
                            <span class="text"> @{{message}}</span>
                            <span class="hover-text">@lang('Copy')</span>
                        </span>
                    </td>
                    <td>@lang('Message')</td>
                </tr>
                @foreach (gs('global_shortcodes') as $shortCode => $codeDetails)
                <tr>
                    <td>
                        <span class="copyBtn cursor-pointer hover-change-text" data-copy="@{{@php echo $shortCode @endphp}}">
                            <span class="text"> @{{@php echo $shortCode @endphp}}</span>
                            <span class="hover-text">@lang('Copy')</span>
                        </span>
                    </td>
                    <td>{{ __($codeDetails) }}</td>
                </tr>
                @endforeach
                {{-- blade-formatter-enable --}}
            </x-panel.ui.table.body>
        </x-panel.ui.table>
    </div>
</div>
