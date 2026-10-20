@include('errors.layout', [
    'codigo' => $exception->getStatusCode(),
    'titulo' => 'Algo deu errado',
    'mensagem' => 'Ocorreu um erro ao processar sua solicitação. Tente novamente em instantes.',
])
