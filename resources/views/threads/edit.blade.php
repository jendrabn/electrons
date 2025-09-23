@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 fw-bold">Buat Thread Baru</h4>
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
                        <form action="{{ route('comunity.store') }}"
                              id="threadForm"
                              method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="title">Judul Thread</label>
                                <input class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       maxlength="120"
                                       name="title"
                                       placeholder="Judul thread..."
                                       required
                                       type="text"
                                       value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="category_ids">Kategori</label>
                                <select class="@error('category_ids') is-invalid @enderror"
                                        id="category_ids"
                                        multiple
                                        name="category_ids[]"
                                        required>
                                    @foreach ($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih satu atau lebih kategori</small>
                                @error('category_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                       for="body">Isi Thread</label>
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
                                <a class="btn btn-outline-secondary"
                                   href="{{ route('comunity.index') }}">Batal</a>
                                <button class="btn btn-primary"
                                        type="submit">
                                    <i class="bi bi-send"></i> Posting
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

            // Submit form: ambil isi Quill ke input hidden
            document.getElementById('threadForm').addEventListener('submit', function(e) {
                document.getElementById('body').value = quill.root.innerHTML;
            });

            const ts = new TomSelect('#category_ids', {
                plugins: ['remove_button'],
            });
        });
    </script>
@endsection
