{{-- Partial: Display Responsive AdSense --}}
@php
    // Otomatis aktifkan test-ad jika bukan production
    $adTest = app()->environment('production') ? null : 'on';
    // Jika slot kosong, beberapa publisher/AdSense mungkin mengembalikan status 'unfilled'.
    // Kita memasang attribute data-ad-status jika perlu ditangani via JS pada client.
@endphp

<div class="d-flex justify-content-center">
    <ins @if ($adTest) data-adtest="on" @endif
         class="adsbygoogle"
         data-ad-client="ca-pub-9750508834370473"
         data-ad-format="auto"
         data-ad-slot="{{ $slot ?? '' }}"
         data-full-width-responsive="true"
         style="display:block;width:100%;height:auto"></ins>
</div>
<script>
    (adsbygoogle = window.adsbygoogle || []).push({});
</script>
