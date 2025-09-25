<!-- Thread delete confirmation modal -->
<div aria-hidden="true"
     aria-labelledby="threadDeleteModalLabel"
     class="modal fade"
     id="threadDeleteModal"
     tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="threadDeleteModalLabel">Konfirmasi Hapus Thread</h5>
                <button aria-label="Close"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus thread ini? Semua komentar juga akan dihapus.
            </div>
            <div class="modal-footer border-top-0">
                <button class="btn btn-link text-decoration-none text-primary"
                        data-bs-dismiss="modal"
                        type="button">Batal</button>
                <button class="btn btn-link text-decoration-none text-danger"
                        id="threadDeleteConfirmBtn"
                        type="button">Hapus</button>
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
     id="commentEditModal"
     tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title"
                    id="replyEditModalLabel">Edit Reply</h5>
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
