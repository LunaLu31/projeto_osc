<?php

/*
| Rotas nomeadas expostas ao front-end React (função route() do ziggy-js).
| As rotas da API e da documentação não são necessárias no site.
*/
return [
    'except' => ['api.*', 'docs.*', 'scramble.*', 'sanctum.*', 'storage.*', 'inertia.*', '_inertia.*'],
];
