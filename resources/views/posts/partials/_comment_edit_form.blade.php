<form action="{{ route('posts.comments.update', [$post->id, $comment->id]) }}"
      id="comment-edit-form"
      method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <textarea class="form-control"
                  name="body"
                  required
                  rows="4">{{ $comment->body }}</textarea>
    </div>

    <div class="text-end">
        <button class="btn btn-link text-decoration-none text-primary"
                data-bs-dismiss="modal"
                type="button">Batal</button>
        <button class="btn btn-link text-decoration-none text-info">Simpan Perubahan</button>
    </div>
</form>
