@php
    $codigo = $exception->getStatusCode();
    [$titulo, $mensagem] = match ($codigo) {
        403 => ['Acesso negado', 'Você não tem permissão para acessar esta página.'],
        404 => ['Página não encontrada', 'O endereço acessado não existe ou foi removido.'],
        419 => ['Sessão expirada', 'Sua sessão expirou. Volte, recarregue a página e tente novamente.'],
        429 => ['Muitas tentativas', 'Você fez muitas tentativas em pouco tempo. Aguarde um minuto.'],
        default => ['Não foi possível continuar', 'Verifique os dados e tente novamente.'],
    };
@endphp
@include('errors.layout')
