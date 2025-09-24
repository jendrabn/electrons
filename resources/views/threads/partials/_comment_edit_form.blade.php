<form action="{{ route('comunity.comments.update', [$comment->thread->slug, $comment->id]) }}"
      id="comment-edit-form"
      method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <div data-content="{{ e($comment->body) }}"
             id="quill-edit-{{ $comment->id }}"
             style="min-height:140px;"></div>
        <input id="edit-body-{{ $comment->id }}"
               name="body"
               type="hidden"
               value="{{ old('body', $comment->body) }}">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-link text-decoration-none text-primary"
                data-bs-dismiss="modal"
                type="button">Batal</button>
        <button class="btn btn-link text-decoration-none text-info">Simpan</button>
    </div>
</form>
