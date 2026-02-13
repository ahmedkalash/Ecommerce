<div class="sticky-top z-3 row gutters-10">
    @php
        $galleryMedia = $detailedProduct->getMedia('gallery');
        $videos = $detailedProduct->video_link;
        $shortVideoMedia = $detailedProduct->getMedia('short_video');
        $videoThumbnailUrl = $detailedProduct->getFirstMediaUrl('video_thumbnail');
    @endphp

        <!-- Gallery Images -->
    <div class="col-12" style="">
        <div style="width: 100%; height: 500px;     align-items: center; justify-content: center; display: flex;"
             class=" aiz-carousel product-gallery arrow-inactive-transparent arrow-lg-none product-gallery-carousel"
             data-nav-for='.product-gallery-thumb' data-fade='true' data-auto-height='true' data-arrows='true'>
            @if ($detailedProduct->digital == 0)
                @foreach ($detailedProduct->stocks as $key => $stock)
                    @if ($stock->image != null)
                        <div class="carousel-box img-zoom rounded-0" style="">
                            <img class="img-fluid lazyload mx-auto" style=""
                                 src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                 data-src="{{ get_file_by_id($stock->image) }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </div>
                    @endif
                @endforeach
            @endif

            @if ($galleryMedia->count() == 1)
                <div class="carousel-box img-zoom rounded-0" style="height: 100%">
                    <img class="img-fluid h-full lazyload mx-auto" style="height: 450px;"
                         src="{{ $galleryMedia->first()->getUrl() }}"
                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            @else
                @foreach ($galleryMedia as $key => $media)
                    <div class="carousel-box img-zoom rounded-0" style="height: 100%">
                        <img class="img-fluid h-full lazyload mx-auto" style="height: 450px;"
                             src="{{ static_asset('assets/img/placeholder.jpg') }}"
                             data-src="{{ $media->getUrl() }}"
                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                    </div>
                @endforeach
            @endif

            @foreach ($shortVideoMedia as $index => $videoMedia)
                <div class="carousel-box img-zoom rounded-0">
                    <div class="video-container">
                        <video class="upload_video" preload="metadata"
                               poster="{{ $videoThumbnailUrl ?: '' }}"
                               disablePictureInPicture>
                            <source src="{{ $videoMedia->getUrl() }}" type="video/mp4">
                        </video>

                        <button class="custom-play-btn playButton">▶</button>

                        <div class="bottom-bar">
                            <button class="playPauseBtn">⏸</button>
                            <span><span class="currentTime">0:00</span> / <span class="duration">0:00</span></span>
                            <button class="muteBtn">🔊</button>
                            <button class="openPopupBtn">⛶</button>
                        </div>

                        <div class="progress-container">
                            <input type="range" class="progress" value="0" step="0.1">
                        </div>
                    </div>
                </div>
            @endforeach


            @if (!empty($videos) && is_iterable($videos))
                @foreach ($videos as $video)
                    <div class="carousel-box img-zoom rounded-0">

                        @php

                            if (preg_match('/shorts/', $video)) {
                                $isShorts = true;
                            } else {
                                $isShorts = false;
                            }

                            $iframeWidth = $isShorts ? 300 : '100%';
                            $iframeHeight = $isShorts ? 450 : 300;
                        @endphp

                        <iframe class="embed-responsive-item" src="{{ convertToEmbedUrl($video) }}"
                                width="{{ $iframeWidth }}" height="{{ $iframeHeight }}" allowfullscreen
                                style="display: block; margin: 0 auto; z-index: 0;">
                        </iframe>

                    </div>
                @endforeach
            @endif

        </div>
    </div>
    <div class="col-12 mt-3 d-none d-lg-block scroll-x">
        <div class="aiz-carousel half-outside-arrow product-gallery-thumb" data-items='7'
             data-nav-for='.product-gallery' data-focus-select='true' data-arrows='true' data-vertical='false'
             data-auto-height='true'>

            @if ($detailedProduct->digital == 0)
                @foreach ($detailedProduct->stocks as $key => $stock)
                    @if ($stock->image != null)
                        <div class="carousel-box c-pointer rounded-0" data-variation="{{ $stock->variant }}">
                            <img class="lazyload mw-100 size-60px mx-auto border p-1"
                                 src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                 data-src="{{ get_file_by_id($stock->image) }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </div>
                    @endif
                @endforeach
            @endif

            @foreach ($galleryMedia as $key => $media)
                <div class="carousel-box c-pointer rounded-0">
                    <img class="lazyload mw-100 size-60px mx-auto border p-1"
                         src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ $media->getUrl() }}"
                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            @endforeach

            @foreach ($shortVideoMedia as $index => $videoMedia)
                <div class="carousel-box c-pointer rounded-0 position-relative" data-variation="short-video">
                    <img class="lazyload mw-100 size-60px mx-auto border p-1"
                         src="{{ static_asset('assets/img/placeholder.jpg') }}"
                         data-src="{{ $videoThumbnailUrl ?: '' }}"
                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">

                    <div
                        style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);color: white;font-size: 24px;background-color: rgba(0, 0, 0, 0.5);border-radius: 50%;width: 32px;height: 32px;display: flex;align-items: center;justify-content: center;">
                        <i class="la la-play"></i>
                    </div>
                </div>
            @endforeach




            @if (!empty($videos) && is_iterable($videos))
                @foreach ($videos as $video)
                    @php
                        $youtube_id = youtubeVideoId($video);
                        $youtube_thumb = 'https://img.youtube.com/vi/' . $youtube_id . '/hqdefault.jpg';
                    @endphp
                    <div class="carousel-box c-pointer rounded-0 position-relative" data-variation="youtube">
                        <img class="mw-100 size-60px mx-auto border p-1" src="{{ $youtube_thumb }}"
                             alt="YouTube Video Thumbnail">

                        <div id="playtimeiconchange"
                             style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);color: white;font-size: 24px;background-color: rgba(0, 0, 0, 0.5);border-radius: 50%;width: 32px;height: 32px;display: flex;align-items: center;justify-content: center;">
                            <i class="la la-play"></i>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>

</div>
