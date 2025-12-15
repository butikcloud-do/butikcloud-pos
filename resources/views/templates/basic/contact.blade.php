@php
    $contactContent = getContent('contact_us.content', true);
    $address = $contactContent->data_values->contact_address;
    $mapUrl = 'https://www.google.com/maps/search/' . urlencode($address);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="contact-page pt-120">
        <div class="container">
            <div class="row gy-4 align-items-center gx-lg-5">
                <div class="col-lg-6">
                    <div class="contact-form">
                        <form method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12 form--group">
                                    <label for="name" class="form--label">@lang('Name')</label>
                                    <input name="name" type="text" class="form-control form--control"
                                        value="{{ old('name', @$user->fullname) }}"
                                        @if ($user && $user->profile_complete) readonly @endif required
                                        placeholder="@lang('Enter name')" id="name">
                                </div>
                                <div class="col-xl-12 form--group">
                                    <label for="email" class="form--label">@lang('Email')</label>
                                    <input name="email" type="text" class="form-control form--control"
                                        placeholder="@lang('Enter your email')" id="email"
                                        value="{{ old('email', @$user->email) }}"
                                        @if ($user) readonly @endif required>
                                </div>
                                <div class="col-12 form--group">
                                    <label for="message" class="form--label">@lang('Message')</label>
                                    <textarea name="message" id="message" class="form-control form--control" cols="30" rows="10"
                                        placeholder="@lang('Write message')" required></textarea>
                                </div>

                                <x-captcha />

                                <div class="col-12 form--group">
                                    <label for="subject" class="form--label">@lang('Subject')</label>
                                    <input class="form--control form-control" type="text" id="subject" name="subject"
                                        placeholder="@lang('Enter your subject')" value="{{ old('subject') }}" required>
                                </div>

                                <div class="col-12 form--group">
                                    <button type="submit" class="btn btn--base w-100">@lang('Send message')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contact_thumb h-100">
                        <img class="fit-image"
                            src="{{ frontendImage('contact_us', $contactContent->data_values->image ?? '', '1250x1475') }}"
                            alt="img">
                    </div>
                </div>

            </div>

            <div class="contact-item-wrapper mt-60">
                <div class="row gy-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__top">
                                <div class="contact-item__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M6.07715 15.2947C8.0498 17.6527 10.4244 19.5093 13.1348 20.823C14.1667 21.3121 15.5467 21.8923 17.0842 21.9917C17.1795 21.9959 17.2707 22 17.366 22C18.3979 22 19.2268 21.6436 19.9023 20.9101C19.9064 20.9059 19.9147 20.8976 19.9188 20.8893C20.1592 20.5993 20.4327 20.3382 20.7187 20.0605C20.9135 19.874 21.1124 19.6792 21.303 19.4803C22.1857 18.5603 22.1857 17.3916 21.2947 16.5006L18.804 14.0099C18.3813 13.5707 17.8757 13.3386 17.3453 13.3386C16.8148 13.3386 16.3051 13.5707 15.8699 14.0058L14.3863 15.4894C14.2496 15.4107 14.1086 15.3402 13.976 15.2739C13.8103 15.191 13.6569 15.1123 13.5202 15.0253C12.1692 14.1674 10.9425 13.0485 9.76965 11.6104C9.17703 10.8603 8.77919 10.2304 8.50152 9.58807C8.89108 9.23581 9.25577 8.86697 9.60803 8.50642C9.73236 8.37795 9.86083 8.24948 9.9893 8.12101C10.4369 7.67344 10.6772 7.15541 10.6772 6.62909C10.6772 6.10278 10.441 5.58475 9.9893 5.13717L8.75432 3.9022C8.60927 3.75715 8.47251 3.61625 8.33161 3.4712C8.05809 3.18939 7.77214 2.8993 7.49033 2.63821C7.06348 2.21964 6.56203 2 6.03157 2C5.50525 2 4.99966 2.21964 4.55623 2.64235L3.00629 4.19229C2.44268 4.75591 2.12357 5.4397 2.05726 6.23125C1.97852 7.22172 2.16087 8.27435 2.63331 9.54662C3.35855 11.5151 4.45262 13.3427 6.07715 15.2947ZM3.06845 6.31828C3.11818 5.76709 3.32954 5.30709 3.72738 4.90924L5.26903 3.36759C5.5094 3.13552 5.77463 3.01533 6.03157 3.01533C6.28437 3.01533 6.54131 3.13552 6.77753 3.37588C7.05519 3.63282 7.31628 3.9022 7.59808 4.18815C7.73899 4.3332 7.88403 4.47824 8.02908 4.62743L9.26406 5.86241C9.521 6.11935 9.65362 6.38044 9.65362 6.63738C9.65362 6.89432 9.521 7.15541 9.26406 7.41235C9.13559 7.54082 9.00712 7.67344 8.87865 7.80191C8.49323 8.19146 8.13269 8.5603 7.73484 8.91256C7.72655 8.92085 7.72241 8.92499 7.71412 8.93328C7.37015 9.27725 7.42403 9.60464 7.50691 9.85329C7.51105 9.86573 7.5152 9.87402 7.51934 9.88645C7.83845 10.6531 8.28188 11.3825 8.97396 12.2528C10.2172 13.7862 11.5268 14.9755 12.969 15.8914C13.1472 16.0075 13.3378 16.0986 13.516 16.1898C13.6818 16.2727 13.8351 16.3514 13.9719 16.4385C13.9885 16.4467 14.0009 16.455 14.0175 16.4633C14.1542 16.5338 14.2869 16.5669 14.4195 16.5669C14.751 16.5669 14.9665 16.3556 15.037 16.2851L16.5869 14.7352C16.8273 14.4948 17.0883 14.3663 17.3453 14.3663C17.6602 14.3663 17.9172 14.5611 18.0788 14.7352L20.5778 17.23C21.0751 17.7273 21.0709 18.2661 20.5653 18.7924C20.3913 18.9789 20.2089 19.1571 20.0142 19.3436C19.7241 19.6254 19.4215 19.9155 19.148 20.2429C18.6714 20.7567 18.1037 20.9971 17.3701 20.9971C17.2997 20.9971 17.2251 20.993 17.1547 20.9888C15.7953 20.9018 14.5314 20.3713 13.5823 19.9196C11.0046 18.6722 8.74189 16.9026 6.86456 14.6564C5.31876 12.7957 4.27856 11.0634 3.59062 9.2068C3.16377 8.06714 3.00214 7.15126 3.06845 6.31828Z"
                                            fill="CurrentColor" stroke="CurrentColor" />
                                    </svg>
                                </div>
                                <p class="contact-item__icon-text">
                                    @lang('Phone Number')
                                </p>
                            </div>
                            <div class="contact-item__bottom">
                                <a href="tel:{{ @$contactContent->data_values->contact_number }}"
                                    class="contact-item__bottom-link">{{ @$contactContent->data_values->contact_number ?? '' }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__top">
                                <div class="contact-item__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M12 21.7035C11.4967 21.7035 11.0183 21.4852 10.6883 21.1068C9.94 20.2468 9.12167 19.4735 8.33 18.7252C5.975 16.4985 3.75 14.3952 3.75 10.5352C3.75 5.98682 7.45167 2.28516 12 2.28516C16.5483 2.28516 20.25 5.98682 20.25 10.5352C20.25 14.3952 18.025 16.4985 15.67 18.7252C14.8783 19.4735 14.06 20.2468 13.3117 21.1068C12.9817 21.4868 12.5033 21.7035 12 21.7035ZM12 3.78349C8.27833 3.78349 5.25 6.81182 5.25 10.5335C5.25 13.7468 7.155 15.5485 9.36167 17.6335C10.18 18.4068 11.025 19.2068 11.8217 20.1218C11.945 20.2635 12.0583 20.2635 12.1817 20.1218C12.9783 19.2068 13.8233 18.4068 14.6417 17.6335C16.8467 15.5485 18.7533 13.7485 18.7533 10.5335C18.7533 6.81182 15.725 3.78349 12.0033 3.78349H12ZM12 14.2835C9.93167 14.2835 8.25 12.6018 8.25 10.5335C8.25 8.46516 9.93167 6.78349 12 6.78349C14.0683 6.78349 15.75 8.46516 15.75 10.5335C15.75 12.6018 14.0683 14.2835 12 14.2835ZM12 8.28349C10.76 8.28349 9.75 9.29349 9.75 10.5335C9.75 11.7735 10.76 12.7835 12 12.7835C13.24 12.7835 14.25 11.7735 14.25 10.5335C14.25 9.29349 13.24 8.28349 12 8.28349Z"
                                            fill="CurrentColor" stroke="CurrentColor" />
                                    </svg>
                                </div>
                                <p class="contact-item__icon-text">
                                    @lang('Office Address')
                                </p>
                            </div>
                            <div class="contact-item__bottom">
                                <a href="{{ $mapUrl }}" class="contact-item__bottom-link" target="_blank">
                                    {{ __(@$contactContent->data_values->contact_address) }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-item__top">
                                <div class="contact-item__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 4C7.58172 4 4 7.58172 4 12C4 13.6921 4.52425 15.2588 5.41916 16.5503C5.71533 16.9778 5.78673 17.5484 5.55213 18.0532L4.64729 20H12C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22H3.86159C2.72736 22 2.00986 20.7933 2.53406 19.8016L3.62175 17.4613C2.59621 15.8909 2 14.0137 2 12Z"
                                            fill="CurrentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 14C8 13.4477 8.44772 13 9 13H15C15.5523 13 16 13.4477 16 14C16 14.5523 15.5523 15 15 15H9C8.44772 15 8 14.5523 8 14Z"
                                            fill="CurrentColor" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 10C8 9.44772 8.44772 9 9 9H11C11.5523 9 12 9.44772 12 10C12 10.5523 11.5523 11 11 11H9C8.44772 11 8 10.5523 8 10Z"
                                            fill="CurrentColor" />
                                    </svg>
                                </div>
                                <p class="contact-item__icon-text">
                                    @lang('Email Address')
                                </p>
                            </div>
                            <div class="contact-item__bottom">
                                <a href="mailto:{{ $contactContent->data_values->email_address ?? '' }}"
                                    class="contact-item__bottom-link">
                                    {{ $contactContent->data_values->email_address ?? '' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
