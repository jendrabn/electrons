{{-- Partial: Display Responsive AdSense --}}
@php
    // Otomatis aktifkan test-ad jika bukan production
    $adTest = app()->environment('production') ? null : 'on';
@endphp

<div class="my-4 d-flex justify-content-center">
    <ins @if ($adTest) data-adtest="on" @endif
         class="adsbygoogle"
         data-ad-client="ca-pub-9750508834370473"
         data-ad-format="auto"
         data-ad-slot="{{ $slot ?? '' }}"
         data-full-width-responsive="true"
         style="display:block;min-height:250px"></ins>
</div>
<script>
    (adsbygoogle = window.adsbygoogle || []).push({});
</script>
