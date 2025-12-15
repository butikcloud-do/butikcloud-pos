  <section class="breadcrumb">
      <div class="container">
          <div class="row justify-content-center">
              <div class="col-lg-8">
                  <div class="breadcrumb__wrapper">
                      <h2 class="breadcrumb__title"> {{ __($pageTitle) }}</h2>
                      <ul class="breadcrumb__list">
                          <li class="breadcrumb__item">
                              <a href="{{ route('home') }}" class="breadcrumb__link">
                                  <i class="las la-home"></i> @lang('Home')</a>
                          </li>
                          <li class="breadcrumb__item">
                              <i class="fas fa-arrow-right"></i>
                          </li>
                          <li class="breadcrumb__item">
                              <span class="breadcrumb__item-text"> {{ __($pageTitle) }} </span>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
  </section>
