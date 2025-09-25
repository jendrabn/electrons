<form action="{{ route('comunity.comments.update', [$comment->thread->id, $comment->id]) }}"
      id="comment-edit-form"
      method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <div class="edit-quill"
             id="edit-quill-editor-{{ $comment->id }}"
             style="height:200px;">{!! $comment->body !!}</div>
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
