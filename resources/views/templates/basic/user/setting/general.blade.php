@extends($activeTemplate . 'layouts.master')
@section('panel')
    <form method="POST">
        <x-panel.ui.card>
            <x-panel.ui.card.body>
                @csrf
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label class="required"> @lang('Timezone')</label>
                        <select class="  form-control select2" name="timezone">
                            @foreach ($timezones as $key => $timezone)
                                <option value="{{ @$key }}" @selected(@$timezone == @$generalSetting->timezone)>
                                    {{ __($timezone) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="required"> @lang('Date Format')</label>
                        <select class="form-control select2" name="date_format" data-minimum-results-for-search="-1">
                            @foreach (supportedDateFormats() as $dateFormat)
                                <option value="{{ @$dateFormat }}" @selected($generalSetting->date_format == $dateFormat)>
                                    {{ $dateFormat }} ({{ date($dateFormat) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="required"> @lang('Time Format')</label>
                        <select class="form-control select2" name="time_format" data-minimum-results-for-search="-1">
                            @foreach (supportedTimeFormats() as $key => $timeFormat)
                                <option value="{{ @$timeFormat }}" @selected($generalSetting->time_format == $timeFormat)>
                                    {{ __($timeFormat) }} ({{ date($timeFormat) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>@lang('Currency')</label>
                            <input class="form-control" type="text" name="cur_text" required
                                value="{{ $generalSetting->cur_text }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>@lang('Currency Symbol')</label>
                            <input class="form-control" type="text" name="cur_sym" required
                                value="{{ $generalSetting->cur_sym }}">
                        </div>
                    </div>
                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Currency Showing Format')</label>
                        <select class="select2 form-control" name="currency_format" data-minimum-results-for-search="-1">
                            <option value="1" @selected($generalSetting->currency_format == Status::CUR_BOTH)>
                                @lang('Show Currency Text and Symbol Both')({{ $generalSetting->cur_sym }}{{ showAmount(100, currencyFormat: false) }}
                                {{ __($generalSetting->cur_text) }})
                            </option>
                            <option value="2" @selected($generalSetting->currency_format == Status::CUR_TEXT)>
                                @lang('Show Currency Text Only')({{ showAmount(100, currencyFormat: false) }}
                                {{ __($generalSetting->cur_text) }})
                            </option>
                            <option value="3" @selected($generalSetting->currency_format == Status::CUR_SYM)>
                                @lang('Show Currency Symbol Only')({{ $generalSetting->cur_sym }}{{ showAmount(100, currencyFormat: false) }})
                            </option>
                        </select>
                    </div>

                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Allow Precision')</label>
                        <select class="select2 form-control" name="allow_precision" data-minimum-results-for-search="-1">
                            @foreach (range(1, 8) as $digit)
                                <option value="{{ $digit }}" @selected($generalSetting->allow_precision == $digit)>
                                    {{ $digit }}
                                    @lang('Digit')({{ showAmount(100, currencyFormat: false, decimal: $digit) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6 ">
                        <label class="required"> @lang('Thousand Separator')</label>
                        <select class="select2 form-control" name="thousand_separator" data-minimum-results-for-search="-1">
                            @foreach (supportedThousandSeparator() as $k => $supportedThousandSeparator)
                                <option value="{{ $k }}" @selected($generalSetting->thousand_separator == $k)>
                                    {{ __($supportedThousandSeparator) }}
                                    @if ($k == 'space')
                                        ({{ showAmount(1000, currencyFormat: false, separator: ' ') }})
                                    @elseif($k == 'none')
                                        (@lang('10000'))
                                    @else
                                        ({{ showAmount(1000, currencyFormat: false, separator: $k) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label> @lang('Record to Display Per Page')</label>
                        <select class="select2 form-control" name="paginate_number" data-minimum-results-for-search="-1">
                            <option value="20" @selected($generalSetting->paginate_number == 20)>@lang('20 items')</option>
                            <option value="50" @selected($generalSetting->paginate_number == 50)>@lang('50 items')</option>
                            <option value="100" @selected($generalSetting->paginate_number == 100)>@lang('100 items')</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <x-panel.ui.btn.submit />
                    </div>
                </div>
            </x-panel.ui.card.body>
        </x-panel.ui.card>
    </form>
@endsection
