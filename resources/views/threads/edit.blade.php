@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 fw-bold">Edit Thread</h4>
                    </div>
                    <div class="card-body">
                        {{-- RULE MEMBUAT THREAD --}}
                        <div class="alert alert-info mb-4">
                            <strong>Aturan Membuat Thread:</strong>
                            <ul class="mb-0">
                                <li>Judul thread harus jelas dan sesuai topik.</li>
                                <li>Pilih kategori yang relevan dengan isi thread.</li>
                                <li>Isi thread minimal 20 karakter dan maksimal 5000 karakter.</li>
                                <li>Dilarang posting konten SARA, spam, atau promosi tanpa izin.</li>
                                <li>Gunakan bahasa yang sopan dan mudah dipahami.</li>
                            </ul>
                        </div>
                        <form action="{{ route('comunity.update', $thread->id) }}"
                              id="threadForm"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="title">Judul</label>
                                <input class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       maxlength="120"
                                       name="title"
                                       placeholder="Judul thread..."
                                       required
                                       type="text"
                                       value="{{ old('title', $thread->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="tag_ids">Tag</label>
                                <select class="@error('tag_ids') is-invalid @enderror"
                                        id="tag_ids"
                                        multiple
                                        name="tag_ids[]"
                                        required>
                                    @php
                                        $selected = old('tag_ids', $thread->tags->pluck('id')->toArray());
                                    @endphp
                                    @foreach ($tags as $id => $name)
                                        <option @if (in_array($id, $selected)) selected @endif
                                                value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih satu atau lebih tag</small>
                                @error('tag_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="body">Pertanyaan</label>
                                <div id="quill-editor"
                                     style="height: 220px;"></div>
                                <input id="body"
                                       name="body"
                                       type="hidden">
                                @error('body')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-light"
                                   href="{{ route('comunity.show', $thread->id) }}">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button class="btn btn-primary"
                                        type="submit">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
          rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css"
          rel="stylesheet">
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Quill
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Tulis isi thread di sini...',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Populate editor with existing thread body (use JSON encoding to avoid HTML-escaping issues)
            const initialBody = {!! json_encode(old('body', $thread->body ?? '')) !!};
            if (initialBody) {
                quill.clipboard.dangerouslyPasteHTML(initialBody);
            }

            // Ensure hidden input contains editor HTML on load and before submit
            document.getElementById('body').value = quill.root.innerHTML;
            document.getElementById('threadForm').addEventListener('submit', function(e) {
                document.getElementById('body').value = quill.root.innerHTML;
            });

            const ts = new TomSelect('#tag_ids', {
                plugins: ['remove_button'],
            });
        });
    </script>
@endsection
