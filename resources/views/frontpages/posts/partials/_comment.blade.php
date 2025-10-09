<div class="comment mb-4"
     id="comment-{{ $comment->id }}">
    <div class="d-flex align-items-start gap-3">
        <a aria-label="Profil {{ $comment->user->name }}"
           class="flex-shrink-0"
           href="{{ route('authors.show', $comment->user->username) }}">
            <img alt="{{ $comment->user->name }}"
                 class="rounded-circle border"
                 height="40"
                 loading="lazy"
                 src="{{ $comment->user->avatar_url }}"
                 style="object-fit:cover"
                 width="40">
        </a>

        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <a class="text-decoration-none fw-semibold link-body-emphasis"
                           href="{{ route('authors.show', $comment->user->username) }}">
                            {{ $comment->user->name }}
                        </a>
                        <small class="text-body-secondary">•</small>
                        <time class="small text-body-secondary"
                              datetime="{{ $comment->created_at->toIso8601String() }}">
                            {{ $comment->created_at->diffForHumans() }}
                        </time>
                    </div>
                </div>

                <div class="d-flex gap-2 ms-3">
                    {{-- Edit: only comment owner can edit (admin cannot edit others) --}}
                    @if (auth()->check() && auth()->id() === $comment->user_id)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-body-emphasis edit-btn comment-edit"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('posts.comments.edit', [$post->id, $comment->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                    @endif

                    {{-- Delete: comment owner OR admin can delete --}}
                    @if (auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin()))
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-danger delete-btn comment-delete"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('posts.comments.destroy', [$post->id, $comment->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 text-body text-break text-wrap comment-body">
                {{ $comment->body }}
            </div>

            <div class="d-flex align-items-center gap-3">
                @if (auth()->check())
                    <button aria-label="Balas komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary reply-btn"
                            data-bs-target="#replyCollapse{{ $comment->id }}"
                            data-bs-toggle="collapse"
                            data-id="{{ $comment->id }}"
                            data-username="{{ $comment->user->username }}"
                            title="Balas komentar"
                            type="button">
                        <i class="bi bi-reply me-1"></i> Balas
                    </button>
                @endif

                <button aria-label="Suka komentar"
                        class="btn btn-link btn-sm p-0 text-decoration-none like-btn comment-like-btn link-body-secondary"
                        data-id="{{ $comment->id }}"
                        data-url="{{ route('posts.comments.like', [$post->id, $comment->id]) }}"
                        title="Suka"
                        type="button">
                    <i
                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }} me-1"></i>
                    <span class="like-count">{{ $comment->likes->count() }}</span>
                </button>

                <button class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary"
                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                        data-bs-toggle="collapse"
                        type="button">
                    <i class="bi bi-chat-left-text me-1"></i>
                    Lihat {{ $comment->replies->count() }} Balasan
                </button>
            </div>

            <div class="collapse mt-3"
                 id="replyCollapse{{ $comment->id }}">
                @auth
                    <form action="{{ route('posts.comments.store', $post->id) }}"
                          class="reply-form"
                          method="POST">
                        @csrf
                        <input name="parent_id"
                               type="hidden"
                               value="{{ $comment->id }}">
                        <textarea class="form-control mb-2"
                                  name="body"
                                  placeholder="Tulis balasan..."
                                  required
                                  rows="3"></textarea>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm"
                                    type="submit">
                                <i class="bi bi-send me-1"></i>Kirim Balasan
                            </button>
                        </div>
                    </form>
                @endauth
            </div>

            <div class="collapse mt-2"
                 id="repliesCollapse{{ $comment->id }}">
                @foreach ($comment->replies as $reply)
                    @include('frontpages.posts.partials._reply', ['reply' => $reply, 'post' => $post])
                @endforeach
            </div>
        </div>
    </div>
</div>
