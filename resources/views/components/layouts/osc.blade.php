{{-- Layout da área da OSC: painel privado com navegação entre as seções. --}}
@props(['title'])
@php
    $osc = auth('osc')->user();
    $secoes = [
        ['rota' => 'osc.painel', 'ativa' => 'osc.painel', 'icone' => 'home', 'rotulo' => 'Visão geral'],
        ['rota' => 'osc.demandas.index', 'ativa' => 'osc.demandas.*', 'icone' => 'box', 'rotulo' => 'Demandas'],
        ['rota' => 'osc.contatos.index', 'ativa' => 'osc.contatos.*', 'icone' => 'people', 'rotulo' => 'Contatos'],
        ['rota' => 'osc.senha.edit', 'ativa' => 'osc.senha.*', 'icone' => 'key', 'rotulo' => 'Senha'],
    ];
@endphp
<x-layouts.painel :title="$title" :nome="$osc->nome" perfil="Perfil da OSC" :iniciais="$osc->iniciais" :inicio="route('osc.painel')" :logout="route('osc.logout')">
    <nav class="painel-nav" aria-label="Seções do painel">
        @foreach ($secoes as $secao)
            <a href="{{ route($secao['rota']) }}" @if (request()->routeIs($secao['ativa'])) aria-current="page" @endif>
                <x-icon :name="$secao['icone']" :size="17" /> {{ $secao['rotulo'] }}
            </a>
        @endforeach
    </nav>

    <x-alerts />

    {{ $slot }}
</x-layouts.painel>
