@foreach ($posts as $post)
    <div class="col-12 col-md-6 col-lg-4">
        <x-post.article :post="$post"
                        role="listitem"
                        variant="vertical" />
    </div>
@endforeach
