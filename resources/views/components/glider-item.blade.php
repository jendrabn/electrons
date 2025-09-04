  <article class="post-item card border-0 h-100 w-100">
      <div class="position-relative">
          <a class="d-block"
             href="{{ route('posts.show', $post->slug) }}">
              <figure class="post-item-image bg-gray-200 rounded-0 overflow-hidden w-100 ratio ratio-16x9 mb-0">
                  <picture>
                      <img alt="{{ $post->image_caption }}"
                           class="h-100 w-100 object-fit-cover"
                           loading="lazy"
                           src="{{ $post->image_url }}" />
                  </picture>

                  <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

                  <div class="position-absolute top-0 start-0 w-100 h-100 p-3 d-flex flex-column justify-content-end">
                      <span class="badge bg-warning rounded-0 align-self-start fw-normal mb-2">
                          {{ $post->category->name ?? '-' }}
                      </span>

                      <a class="text-decoration-none text-white"
                         href="{{ route('posts.show', $post->slug) }}">
                          <h5 class="fw-semibold text-shadow-lg line-clamp-2">{{ $post->title }}
                          </h5>
                      </a>

                      <p class="small mb-0 text-white">
                          <a class="fw-semibold text-decoration-none text-white"
                             href="{{ route('posts.author', $post->user->id) }}">
                              {{ str()->words($post->user->name, 2, '') }}
                          </a>
                          <span class="mx-1">•</span>
                          {{ $post->created_at->format('d M Y') }}
                          <span class="mx-1">•</span>
                          {{ $post->min_read }} min read
                      </p>
                  </div>
              </figure>
          </a>
      </div>
  </article>
