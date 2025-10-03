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
                        <form action="{{ route('community.store') }}"
                              id="threadForm"
                              method="POST">
                            @csrf
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
                                       value="{{ old('title') }}">
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
                                    @foreach ($tags as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
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
                                <a class="btn btn-default"
                                   href="{{ route('community.index') }}">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
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

            // Versi minimal yang lebih aman - ganti bagian image handler dengan ini:
            const toolbar = quill.getModule('toolbar');
            toolbar.addHandler('image', function(value) {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = async function() {
                    const file = input.files[0];
                    if (!file) return;

                    try {
                        const url = await uploadToServer(file);

                        // Method paling sederhana - tambahkan di akhir dokumen
                        quill.focus();

                        // Dapatkan panjang dokumen
                        const length = quill.getLength();

                        // Insert di posisi akhir yang aman
                        quill.insertEmbed(Math.max(0, length - 1), 'image', url);

                        // Tambahkan newline setelah gambar
                        quill.insertText(length, '\n');

                        console.log('Image inserted successfully');

                    } catch (error) {
                        console.error('Upload/Insert error:', error);
                        alert('Gagal mengunggah gambar: ' + error.message);
                    }
                }

                input.click();
            });

            // Fungsi upload yang sama
            async function uploadToServer(file) {
                const form = new FormData();
                form.append('image', file);

                const res = await fetch("{{ route('threads.upload-image') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: form
                });

                if (!res.ok) {
                    const text = await res.text();
                    throw new Error(text || ('HTTP ' + res.status));
                }

                const data = await res.json();

                if (!data.url) {
                    throw new Error('Server tidak mengembalikan URL gambar');
                }

                return data.url;
            }

            function insertEditorImage(url) {
                try {
                    console.log('Attempting to insert image:', url);

                    // Pastikan editor ada dan bisa diakses
                    if (!quill) {
                        throw new Error('Quill editor not available');
                    }

                    // Focus editor
                    quill.focus();

                    // Dapatkan panjang dokumen saat ini
                    const documentLength = quill.getLength();
                    console.log('Document length:', documentLength);

                    // Dapatkan selection saat ini
                    let range = quill.getSelection(true);
                    console.log('Current selection:', range);

                    // Tentukan index yang aman untuk insert
                    let insertIndex;
                    if (range && range.index !== undefined && range.index >= 0) {
                        // Pastikan index tidak melebihi panjang dokumen
                        insertIndex = Math.min(range.index, documentLength - 1);
                    } else {
                        // Jika tidak ada selection atau invalid, gunakan posisi akhir yang aman
                        insertIndex = Math.max(0, documentLength - 1);
                    }

                    // Pastikan insertIndex tidak negatif
                    insertIndex = Math.max(0, insertIndex);

                    console.log('Insert index:', insertIndex);

                    // Validasi index sekali lagi
                    if (insertIndex >= documentLength) {
                        insertIndex = documentLength - 1;
                    }
                    if (insertIndex < 0) {
                        insertIndex = 0;
                    }

                    console.log('Final insert index:', insertIndex);

                    // Insert gambar dengan error handling
                    try {
                        quill.insertEmbed(insertIndex, 'image', url, 'user');
                        console.log('Image embed successful');
                    } catch (embedError) {
                        console.error('Error in insertEmbed:', embedError);
                        // Fallback: coba insert di posisi 0
                        quill.insertEmbed(0, 'image', url, 'user');
                        insertIndex = 0;
                    }

                    // Set selection setelah gambar
                    try {
                        const newPosition = insertIndex + 1;
                        if (newPosition <= quill.getLength()) {
                            quill.setSelection(newPosition, 0);
                        }
                    } catch (selectionError) {
                        console.error('Error setting selection:', selectionError);
                        // Ignore selection error, gambar sudah berhasil diinsert
                    }

                    console.log('Image insertion completed successfully');

                } catch (error) {
                    console.error('Error in insertEditorImage:', error);
                    alert('Gagal menambahkan gambar ke editor. Silakan coba refresh halaman.');
                }
            }

            // Alternative simple version jika masih error
            function insertEditorImageSimple(url) {
                try {
                    console.log('Using simple image insertion for:', url);

                    // Method paling sederhana - selalu insert di akhir
                    const length = quill.getLength();
                    const safeIndex = Math.max(0, length - 1);

                    quill.insertEmbed(safeIndex, 'image', url);
                    quill.setSelection(safeIndex + 1);

                    console.log('Simple image insertion successful');

                } catch (error) {
                    console.error('Error in simple image insertion:', error);

                    // Last resort - insert dengan innerHTML
                    try {
                        const currentHtml = quill.root.innerHTML;
                        const imageHtml = `<img src="${url}" alt="Uploaded image">`;
                        quill.root.innerHTML = currentHtml + '<p>' + imageHtml + '</p>';
                        console.log('Fallback image insertion successful');
                    } catch (fallbackError) {
                        console.error('All image insertion methods failed:', fallbackError);
                        alert('Tidak dapat menambahkan gambar. Silakan coba lagi atau refresh halaman.');
                    }
                }
            }


            // Submit form: ambil isi Quill ke input hidden
            document.getElementById('threadForm').addEventListener('submit', function(e) {
                document.getElementById('body').value = quill.root.innerHTML;
            });

            const ts = new TomSelect('#tag_ids', {
                plugins: ['remove_button'],
            });
        });
    </script>
@endsection
