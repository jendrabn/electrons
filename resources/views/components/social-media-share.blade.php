@props([
    'justify' => 'center', // start, center, end, between, around
    'shape' => 'circle', // circle or square
    'showLabel' => true,
    'size' => 36,
    'gap' => 3,
    'post',
])

@php
    $shapeClass = $shape === 'circle' ? 'rounded-circle' : 'rounded-0';
@endphp

<div class="d-flex flex-wrap gap-{{ $gap }} justify-content-{{ $justify }}">
    {{-- WhatsApp --}}
    <div class="text-center">
        <a class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
           href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}"
           style="background:#25D366; width:{{ $size }}px; height:{{ $size }}px;"
           target="_blank">
            <i class="bi bi-whatsapp fs-5"></i>
        </a>
        @if ($showLabel)
            <div class="small text-muted">WhatsApp</div>
        @endif
    </div>

    {{-- Facebook --}}
    <div class="text-center">
        <a class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
           style="background:#1877F3; width:{{ $size }}px; height:{{ $size }}px;"
           target="_blank">
            <i class="bi bi-facebook fs-5"></i>
        </a>
        @if ($showLabel)
            <div class="small text-muted">Facebook</div>
        @endif
    </div>

    {{-- X / Twitter --}}
    <div class="text-center">
        <a class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
           href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
           style="background:#000; width:{{ $size }}px; height:{{ $size }}px;"
           target="_blank">
            <i class="bi bi-twitter-x fs-5"></i>
        </a>
        @if ($showLabel)
            <div class="small text-muted">X</div>
        @endif
    </div>

    {{-- Telegram --}}
    <div class="text-center">
        <a class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
           href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
           style="background:#229ED9; width:{{ $size }}px; height:{{ $size }}px;"
           target="_blank">
            <i class="bi bi-telegram fs-5"></i>
        </a>
        @if ($showLabel)
            <div class="small text-muted">Telegram</div>
        @endif
    </div>

    {{-- Threads --}}
    <div class="text-center">
        <a class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
           href="https://www.threads.net/intent/post?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
           style="background:#000; width:{{ $size }}px; height:{{ $size }}px;"
           target="_blank">
            <i class="bi bi-threads fs-5"></i>
        </a>
        @if ($showLabel)
            <div class="small text-muted">Threads</div>
        @endif
    </div>

    {{-- Copy Link --}}
    <div class="text-center">
        <button class="btn btn-share-social text-white shadow-sm {{ $shapeClass }} d-flex align-items-center justify-content-center mx-auto mb-1"
                id="btn-copy-link"
                style="background:#6c757d; width:{{ $size }}px; height:{{ $size }}px;"
                type="button">
            <i class="bi bi-link-45deg fs-5"></i>
        </button>
        @if ($showLabel)
            <div class="small text-muted">Copy Link</div>
        @endif
    </div>
</div>
