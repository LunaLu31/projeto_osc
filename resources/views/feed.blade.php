<!DOCTYPE html>
<html>
<head>
<title>Projeto OSC - Feed</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
<h1>Demandas das OSCs</h1>
@if(session('success')) <div class="alert alert-success">{{session('success')}}</div> @endif

@foreach($demandas as $d)
<div class="card mb-3">
<div class="card-body">
<h5>{{$d->titulo}} <span class="badge bg-primary">{{$d->tipo}}</span></h5>
<p>{{$d->descricao}}</p>
<small>OSC: {{$d->osc->nome ?? 'OSC Teste'}} | {{$d->cidade}} - {{$d->bairro}}</small>

<form action="/manifestar" method="POST" class="mt-3">
@csrf
<input type="hidden" name="id_demanda" value="{{$d->id_demanda}}">
<div class="row">
<div class="col"><input name="nome_apoiador" placeholder="Seu nome" class="form-control" required></div>
<div class="col"><input name="telefone" placeholder="WhatsApp" class="form-control" required></div>
<div class="col">
<select name="tipo_interesse" class="form-control" required>
<option value="doacao">Doação</option>
<option value="voluntariado">Voluntariado</option>
<option value="parceria">Parceria</option>
</select>
</div>
<div class="col"><button class="btn btn-success">Quero Ajudar</button></div>
</div>
</form>

</div>
</div>
@endforeach

@if($demandas->isEmpty())
<div class="alert alert-warning">Nenhuma demanda ainda. Cadastre uma no banco pra testar!</div>
@endif

</div>
</body>
</html>