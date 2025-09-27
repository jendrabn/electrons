<!-- Delete confirmation modal for comments -->
<div aria-hidden="true"
     aria-labelledby="commentDeleteModalLabel"
     class="modal fade"
     id="commentDeleteModal"
     tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="commentDeleteModalLabel">Konfirmasi Hapus</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body">
                <!-- message set dynamically -->
            </div>
            <div class="modal-footer border-top-0">
                <button class="btn btn-link text-decoration-none text-primary"
                        data-bs-dismiss="modal"
                        type="button">Batal</button>
                <button class="btn btn-link text-decoration-none text-danger"
                        id="commentDeleteConfirmBtn"
                        type="button">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Social Media Share -->
<div aria-hidden="true"
     aria-labelledby="shareModalLabel"
     class="modal fade"
     id="shareModal"
     tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header d-flex align-items-start border-bottom-0">
                <h5 class="modal-title text-wrap text-truncate fs-6 line-clamp-2"
                    id="shareModalLabel">
                    {{ $post->title }}
                </h5>
                <button aria-label="Tutup"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>

            <div class="modal-body">
                <p class="mb-4 text-center">Bagikan artikel ini melalui:</p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @php
                        $url = urlencode(request()->fullUrl());
                        $title = urlencode($post->title);
                    @endphp

                    @php
                        $buttons = [
                            [
                                'key' => 'whatsapp',
                                'label' => 'WhatsApp',
                                'color' => '#25D366', // WhatsApp Green
                                'icon' => 'bi-whatsapp',
                                'url' => "https://wa.me/?text={$title}%20{$url}",
                            ],
                            [
                                'key' => 'facebook',
                                'label' => 'Facebook',
                                'color' => '#1877F2', // Facebook Blue
                                'icon' => 'bi-facebook',
                                'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
                            ],
                            [
                                'key' => 'instagram',
                                'label' => 'Instagram',
                                'color' => '#E4405F', // Instagram "pink" brand (single color)
                                'icon' => 'bi-instagram',
                                'url' => "https://www.instagram.com/?url={$url}",
                            ],
                            [
                                'key' => 'x',
                                'label' => 'X',
                                'color' => '#000000', // X (Twitter) is black now
                                'icon' => 'bi-twitter-x', // gunakan 'bi-twitter-x' jika tersedia; kalau tidak, pakai SVG custom
                                'url' => "https://twitter.com/intent/tweet?text={$title}%20{$url}",
                            ],
                            [
                                'key' => 'line',
                                'label' => 'LINE',
                                'color' => '#06C755', // LINE Green (brand terbaru)
                                'icon' => 'bi-chat-left-text',
                                'url' => "https://line.me/R/msg/text/?{$title}%20{$url}",
                            ],
                            [
                                'key' => 'telegram',
                                'label' => 'Telegram',
                                'color' => '#26A5E4', // Telegram Blue
                                'icon' => 'bi-telegram',
                                'url' => "https://t.me/share/url?url={$url}&text={$title}",
                            ],
                            [
                                'key' => 'threads',
                                'label' => 'Threads',
                                'color' => '#000000', // Threads Black
                                'icon' => 'bi-threads', // jika tidak ada di Bootstrap Icons versi kamu, pakai SVG custom
                                'url' => "https://www.threads.net/t?text={$title}%20{$url}",
                            ],
                            [
                                'key' => 'linkedin',
                                'label' => 'LinkedIn',
                                'color' => '#0A66C2', // LinkedIn Blue
                                'icon' => 'bi-linkedin',
                                'url' => "https://www.linkedin.com/shareArticle?mini=true&url={$url}&title={$title}",
                            ],
                        ];

                    @endphp

                    @foreach ($buttons as $btn)
                        <div class="text-center"
                             style="width:72px">
                            <a class="d-inline-flex align-items-center justify-content-center rounded-circle"
                               href="{{ $btn['url'] }}"
                               rel="noopener noreferrer"
                               style="width:32px;height:32px;background:{{ $btn['color'] }};color:#fff;text-decoration:none;border-radius:50%"
                               target="_blank">
                                {{-- Bootstrap Icon for the network --}}
                                <i class="bi {{ $btn['icon'] }}"
                                   style="font-size:14px"></i>
                            </a>
                            <div class="mt-1 small text-muted">{{ $btn['label'] }}</div>
                        </div>
                    @endforeach

                    {{-- Copy link button --}}
                    <div class="text-center"
                         style="width:72px">
                        <button class="btn btn-light d-inline-flex align-items-center justify-content-center rounded-circle"
                                id="shareCopyLinkBtn"
                                style="width:32px;height:32px;padding:0;border:1px solid #e9ecef"
                                type="button">
                            <i class="bi bi-link-45deg"></i>
                        </button>
                        <div class="mt-1 small text-muted">Salin Link</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit comment modal (body will be injected via AJAX) -->
<div aria-hidden="true"
     aria-labelledby="commentEditModalLabel"
     class="modal fade"
     id="commentEditModal"
     tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="commentEditModalLabel">Edit Komentar</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body"
                 id="commentEditModalBody">
                <!-- HTML form will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Edit reply modal (body will be injected via AJAX) -->
<div aria-hidden="true"
     aria-labelledby="replyEditModalLabel"
     class="modal fade"
     id="replyEditModal"
     tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="replyEditModalLabel">Edit Balasan</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body"
                 id="replyEditModalBody">
                <!-- HTML form will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>
