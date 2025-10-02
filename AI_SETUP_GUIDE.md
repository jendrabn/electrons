# 🤖 AI Content Generator Setup Guide

## 📋 Status Diagnosis

Berdasarkan log yang terkumpul, berikut adalah analisis error yang terjadi:

### ❌ **Current Issues:**

1. **API URL**: `https://agentrouter.org/v1/chat/completions`
   - Ini bukan endpoint OpenAI resmi
   - Menggunakan service third-party yang memerlukan autentikasi berbeda

2. **Model**: `gpt-5`
   - Model ini tidak tersedia di OpenAI
   - Model yang valid: `gpt-4o-mini`, `gpt-4o`, `gpt-3.5-turbo`

3. **Authentication Error**: Status 401 UNAUTHENTICATED
   - API key tidak valid untuk service yang sedang digunakan

## 🔧 **Solusi untuk OpenAI Resmi:**

### 1. Update file `.env`:
```env
# OpenAI Configuration (Official)
OPENAI_API_KEY=sk-your-actual-openai-api-key-here
OPENAI_API_URL=https://api.openai.com/v1/chat/completions
OPENAI_MODEL=gpt-4o-mini
```

### 2. Mendapatkan API Key OpenAI:
- Kunjungi: https://platform.openai.com/api-keys
- Login dan buat API key baru
- Copy key yang dimulai dengan `sk-`

## 🔧 **Alternatif untuk Service Third-Party:**

Jika Anda ingin tetap menggunakan `agentrouter.org`:

### 1. Update file `.env`:
```env
# AgentRouter Configuration
OPENAI_API_KEY=your-agentrouter-api-key
OPENAI_API_URL=https://agentrouter.org/v1/chat/completions
OPENAI_MODEL=gpt-4o-mini
```

### 2. Cek dokumentasi AgentRouter:
- Pastikan format API key yang benar
- Cek model yang supported
- Verifikasi endpoint URL

## 🧪 **Testing Configuration:**

Setelah update konfigurasi, test dengan:

```bash
php artisan tinker
```

```php
$aiService = app(\App\Services\AIContentGeneratorService::class);
$result = $aiService->generateContent([
    'topic' => 'Test Laravel Tutorial',
    'difficulty' => 'Pemula',
    'programming_language' => 'PHP'
]);
return $result;
```

## 📝 **Monitoring Logs:**

Untuk melihat log debug real-time:

```bash
tail -f storage/logs/laravel.log
```

Atau melalui Boost:
```bash
php artisan tinker
```
```php
// Lihat 20 log entries terakhir
\App\Services\AIContentGeneratorService::readLogEntries(20);
```

## ✅ **Expected Success Response:**

Ketika berhasil, Anda akan melihat log seperti ini:
```
[INFO] OpenAI API Request Started
[INFO] OpenAI Request Payload
[INFO] OpenAI API Response {"status":200,"successful":true}
[INFO] OpenAI API Success {"content_length":2500}
```

## 🚨 **Common Issues:**

1. **Invalid API Key**: Error 401
   - Periksa format key (harus dimulai dengan `sk-` untuk OpenAI)
   - Pastikan key masih aktif

2. **Rate Limiting**: Error 429
   - Tunggu beberapa menit sebelum mencoba lagi
   - Upgrade plan OpenAI jika perlu

3. **Model Not Found**: Error 404
   - Gunakan model yang valid: `gpt-4o-mini`, `gpt-4o`, `gpt-3.5-turbo`

4. **Network Issues**: Timeout/Connection errors
   - Periksa koneksi internet
   - Coba dengan timeout yang lebih tinggi

## 🎯 **Recommended Configuration:**

Untuk penggunaan optimal:

```env
OPENAI_API_KEY=sk-your-key-here
OPENAI_API_URL=https://api.openai.com/v1/chat/completions
OPENAI_MODEL=gpt-4o-mini
```

Model `gpt-4o-mini` direkomendasikan karena:
- ✅ Cost-effective
- ✅ Fast response time
- ✅ Good quality untuk tutorial content
- ✅ Support Indonesian language
