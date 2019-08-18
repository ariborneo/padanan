@extends('layouts.app')

@push('metadata')
  <meta property="og:title" content="{{ $title }}" />
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:url" content="{{ \Illuminate\Support\Facades\Request::fullUrl() }}" />
@endpush

@section('content')
  <div class="container">
    @if (request('katakunci'))
    <div id="doc-header" class="doc-header text-center">
      <h1 class="doc-title"><span aria-hidden="true" class="icon icon_search-2"></span> {{ request('katakunci') }}</h1>
      <div class="meta">@lang('ditemukan :count hasil pencarian untuk katakunci tersebut', ['count' => $words->total()])</div>
      <hr>
    </div>
    @endif

    <div id="search-result" class="doc-body row">
      <div class="doc-content col-md-9 col-12 order-1">

        <div class="content-inner">
          @if ($words->total() == 0)
            <p>@lang('Penelusuran Anda - <strong>:keyword</strong> - tidak cocok dengan dokumen apa pun.', ['keyword' => request('katakunci')])</p>
            <p>@lang('Saran:')</p>
            <ul>
              <li>@lang('Pastikan katakunci diisi.')</li>
              <li>@lang('Pastikan semua kata dieja dengan benar.')</li>
              <li>@lang('Coba kata kunci yang lain.')</li>
              <li>@lang('Coba kata kunci yang lebih umum.')</li>
            </ul>
          @endif

          @foreach ($words as $index => $word)
            @include('word.content', compact('word', 'index'))
          @endforeach
        </div><!--//content-inner-->

        {{ $words->onEachSide(1)->links() }}
      </div><!--//doc-content-->
      <div class="doc-sidebar col-md-3 col-12 order-0 d-none d-md-flex">
        <div id="doc-nav" class="doc-nav">
          <nav id="doc-menu" class="nav doc-menu flex-column sticky">
            @foreach ($words as $word)
            <a class="nav-link scrollto" href="#{{ $word->slug }}">{{ $word->origin }} ({{ strtolower($word->category->name) }})</a>
            @endforeach
          </nav><!--//doc-menu-->
        </div>
      </div><!--//doc-sidebar-->
    </div><!--//doc-body-->
  </div>
@endsection
