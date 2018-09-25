@extends('layout.site')

@section('conteudo')
    <div class="container">

        <form method="GET" action="/characters/search">
           <div class="input-field inline">
                        <input id="query" name="query" type="text" class="validate" required value="{{ $query ?? '' }}">
                        <label for="query" data-error="Preencha o campo Nome" data-error="wrong" data-success="right">Name</label>
                    </div>
                    <button class="btn waves-effect blue darken-1" type="submit">Pesquisar
                        <i class="material-icons right">search</i>
                    </button>
                   
                </form>
        
        <div class="row">
            @if(count($characters) == 0)
                <p>Super Heroi<strong>"{{ $query }}"</strong> não encontrado.</p>
            @endif
            @foreach($characters as $character)
                  <div class="col s4">
                    <div class="card z-depth-2">
                        <div class="card-image waves-effect waves-block waves-light">
                            <img class="activator" src="{{ $character['thumbnail']['path'] }}/portrait_incredible.jpg">
                        </div>
                        <div class="card-content">
                            <span class="card-title activator grey-text text-darken-4" style="font-size: 20px" title="{{ $character['name'] }}">{{ str_limit($character['name'], 18) }}<i class="material-icons right">more_vert</i></span>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4" style="font-size: 15px">{{ $character['name'] }}<i class="material-icons right">close</i></span>
                            <hr>
                            @if($character['description'] !== '')
                                <p class="blue-text text-darken-4">Description:</p>
                                <p>{{ $character['description'] }}</p>
                            @endif
                            @if($character['series']['available'] > 0)
                                <p class="blue-text text-darken-4">Series:</p>
                                <ul class="collection">
                                    @foreach($character['series']['items'] as $serie)
                                        <li class="collection-item">{{ $serie['name'] }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @if($query == '')
        <div class="row center">
            <div class="col s12">
                {!! $characters->setPath('characters')->render() !!}
            </div>
        </div>
    @endif
</div>
@stop
