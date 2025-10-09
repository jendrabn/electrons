@php
    // Aktifkan test-ad otomatis jika bukan production
    $adTest = app()->environment('production') ? null : 'on';
@endphp

<div class="d-flex justify-content-center w-100 text-center my-4">
    <ins @if ($adTest) data-adtest="on" @endif
         class="adsbygoogle"
         data-ad-client="ca-pub-9750508834370473"
         data-ad-format="auto"
         data-ad-slot="{{ $slot ?? '' }}"
         data-full-width-responsive="true"
         style="display:block;width:100%;height:auto"></ins>
</div>
