<x-filament::button :href="route('auth.google')"
                    color="gray"
                    tag="a">
    <span class="flex items-center gap-2">
        <img alt="Google logo"
             src="{{ asset('images/ic_google.svg') }}"
             style="width: 20px; height: 20px; margin-right: 5px">
        Continue with Google
    </span>
</x-filament::button>
