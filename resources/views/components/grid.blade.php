{!! isset($blok['_editable']) ? $blok['_editable'] : '' !!}
<div class="grid">
  @foreach(array_get($blok, 'columns', []) as $blok)
      @include('components/' . $blok['component'], ['blok' => $blok])
  @endforeach
</div>