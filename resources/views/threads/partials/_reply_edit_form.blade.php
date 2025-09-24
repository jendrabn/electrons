<form action="{{ route('comunity.comments.replies.update', [$comment->thread->id, $comment->id, $reply->id]) }}"
      id="reply-edit-form"
      method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <textarea class="form-control"
                  name="body"
                  required
                  rows="4">{{ old('body', $reply->body) }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-link text-decoration-none text-primary"
                data-bs-dismiss="modal"
                type="button">Batal</button>
        <button class="btn btn-link text-decoration-none text-info">Simpan</button>
    </div>
</form>
