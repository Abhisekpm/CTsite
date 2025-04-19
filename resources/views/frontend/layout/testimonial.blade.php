        <!-- Testimonial Start -->
        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="text-center">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Testimonial</h5>
                    <h1 class="mb-5">Our Clients Say!!!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel mt-20">
                    @foreach ($testimonials as $testi)
                    <div class="testimonial-item bg-transparent border-thick rounded p-4">
                        <!--<i class="fa fa-quote-left fa-2x text-primary mb-3"></i>-->
                        {!! Illuminate\Support\Str::limit($testi->quote, 93) !!} <span><a data-bs-toggle="modal" href="#model_test_{{$testi->id}}" role="button">Read More</a></span>
                        <div class="d-flex align-items-center mt-20">
                            @if ($testi->image)
                                <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/testimonials/'.$testi->image) }}"
                                style="width: 50px; height: 50px;">
                            @endif
                            <div class="ps-3">
                                <h5 class="mb-1">{{$testi->name}}</h5>
                                {{-- <small>{{$testi->position}}</small> --}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Testimonial End -->
        @foreach ($testimonials as $testi)
        <div class="modal fade" id="model_test_{{$testi->id}}" aria-labelledby="model_test_{{$testi->id}}" tabindex="-1" style="display: none;" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">@if ($testi->image)<img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/testimonials/'.$testi->image) }}"
                                style="width: 50px; height: 50px;">&nbsp;&nbsp;@endif{{$testi->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                {!! $testi->quote !!}
              </div>
            </div>
          </div>
        </div>
        @endforeach
