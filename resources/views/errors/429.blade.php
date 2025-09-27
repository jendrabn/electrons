@extends('layouts.error')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('message', 'Anda telah mengirim terlalu banyak permintaan dalam waktu singkat. Silakan kurangi dan coba lagi
    nanti.')
