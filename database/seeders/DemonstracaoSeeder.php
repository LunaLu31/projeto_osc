<?php

namespace Database\Seeders;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Rede fictícia para apresentação: php artisan db:seed --class=DemonstracaoSeeder
 *
 * Dois administradores (o padrão do AdmSeeder com 10 OSCs e a coordenação com 15),
 * demandas em todos os status, manifestações nos últimos 6 meses, prazos vencidos
 * e OSCs sem demandas ativas, para que todas as telas tenham o que mostrar.
 * Nomes e contatos são fictícios. Admins: senha "admin123"; OSCs: "senha-segura".
 * Os sorteios usam semente fixa: rodar de novo num banco limpo gera os mesmos dados.
 */
class DemonstracaoSeeder extends Seeder
{
    private const SEMENTE = 2026;

    private const SENHA_ADMINS = 'admin123';

    /** OSCs ficam sem demandas ativas: só concluídas, ou nenhuma publicada. */
    private const SO_CONCLUIDAS = 'so_concluidas';

    private const SEM_DEMANDAS = 'sem_demandas';

    /**
     * OSCs de cada administrador: [nome, área de atuação, bairro, descrição, situação?].
     *
     * @var array<string, list<array{0: string, 1: string, 2: string, 3: string, 4?: string}>>
     */
    private const OSCS_POR_ADMIN = [
        'admin@uoscc.org' => [
            ['Associação Comunitária Gleba A', 'Assistência social', 'Gleba A', 'Atende famílias em vulnerabilidade com distribuição de alimentos e orientação social.'],
            ['Instituto Semear Camaçari', 'Educação', 'Centro', 'Reforço escolar e oficinas de leitura para crianças do ensino fundamental.'],
            ['Casa de Apoio Mãos Dadas', 'Saúde', 'PHOC II', 'Acolhe pacientes em tratamento e seus acompanhantes vindos do interior.'],
            ['Projeto Bola na Rede', 'Esporte e lazer', 'Parque Verde', 'Escolinha de futebol gratuita que usa o esporte para manter jovens na escola.'],
            ['Grupo Cultural Raízes do Recôncavo', 'Cultura', 'Gravatá', 'Oficinas de percussão, dança afro e teatro para adolescentes.'],
            ['Lar dos Idosos Vovó Ritinha', 'Idosos', 'Camaçari de Dentro', 'Instituição de longa permanência para idosos sem apoio familiar.', self::SO_CONCLUIDAS],
            ['Rede Mulheres de Fibra', 'Defesa de direitos', 'Nova Vitória', 'Acolhimento e capacitação profissional de mulheres em situação de violência.'],
            ['Associação Verde Jacuípe', 'Meio ambiente', 'Barra do Jacuípe', 'Preservação do rio Jacuípe, mutirões de limpeza e educação ambiental.'],
            ['Centro Comunitário Nova Aliança', 'Assistência social', 'Lama Preta', 'Cozinha comunitária e atividades de convivência para famílias do bairro.'],
            ['Escolinha Pequenos Passos', 'Educação', 'Ponto Certo', 'Educação infantil comunitária para crianças de 2 a 5 anos.'],
        ],
        'coordenacao@uoscc.org' => [
            ['Instituto Arembepe Sustentável', 'Meio ambiente', 'Arembepe', 'Proteção das tartarugas marinhas e da restinga na orla de Arembepe.'],
            ['Associação Abraçar', 'Saúde', 'Verde Horizonte', 'Apoio a famílias de pessoas com deficiência e terapias complementares.'],
            ['Movimento Jovem Monte Gordo', 'Esporte e lazer', 'Monte Gordo', 'Surfe, vôlei de praia e capoeira para jovens da comunidade pesqueira.'],
            ['Cozinha Solidária Dois de Julho', 'Assistência social', 'Dois de Julho', 'Serve refeições diárias para pessoas em situação de rua.'],
            ['Coletivo Leitura Viva', 'Educação', 'Natal', 'Bibliotecas comunitárias e rodas de leitura em praças do bairro.'],
            ['Filarmônica Lira Camaçariense', 'Cultura', 'Centro', 'Ensino gratuito de instrumentos de sopro e percussão.'],
            ['Casa Abrigo Esperança', 'Defesa de direitos', 'Jardim Limoeiro', 'Abrigo temporário para crianças e adolescentes afastados da família.', self::SEM_DEMANDAS],
            ['Associação Guarajuba Solidária', 'Assistência social', 'Guarajuba', 'Campanhas de arrecadação para famílias de trabalhadores sazonais do litoral.'],
            ['Centro de Convivência Melhor Idade', 'Idosos', 'Alto da Cruz', 'Atividades físicas, artesanato e passeios para idosos.'],
            ['Projeto Capoeira Ginga Livre', 'Cultura', 'Piaçaveira', 'Capoeira e música para crianças no contraturno escolar.'],
            ['Instituto Vozes da Periferia', 'Defesa de direitos', 'Parque das Mangabas', 'Orientação jurídica gratuita e formação de lideranças comunitárias.'],
            ['Associação Pró-Saúde Vila de Abrantes', 'Saúde', 'Vila de Abrantes', 'Mutirões de saúde, aferição de pressão e campanhas de prevenção.'],
            ['Pré-Vestibular Comunitário Acesso', 'Educação', 'PHOC III', 'Preparação gratuita para o ENEM para estudantes de escola pública.'],
            ['Clube de Mães Santa Maria', 'Assistência social', 'Santa Maria', 'Apoio a gestantes e mães com enxoval, cursos e grupos de conversa.', self::SO_CONCLUIDAS],
            ['Recicla Camaçari', 'Meio ambiente', 'Jardim Brasil', 'Cooperativa de catadores e pontos de coleta seletiva nos bairros.'],
        ],
    ];

    /** Categorias que cada área costuma pedir (a primeira é a mais frequente). */
    private const CATEGORIAS_POR_AREA = [
        'Assistência social' => ['Alimentos', 'Higiene', 'Vestuário', 'Voluntariado'],
        'Educação' => ['Educação', 'Materiais', 'Voluntariado', 'Alimentos'],
        'Saúde' => ['Saúde', 'Higiene', 'Voluntariado', 'Materiais'],
        'Esporte e lazer' => ['Materiais', 'Vestuário', 'Voluntariado', 'Outros'],
        'Cultura' => ['Materiais', 'Voluntariado', 'Outros', 'Educação'],
        'Idosos' => ['Higiene', 'Saúde', 'Alimentos', 'Voluntariado'],
        'Defesa de direitos' => ['Voluntariado', 'Higiene', 'Vestuário', 'Educação'],
        'Meio ambiente' => ['Voluntariado', 'Materiais', 'Outros', 'Educação'],
    ];

    /** Títulos de demanda por categoria: [título, faixa de quantidade]. */
    private const DEMANDAS = [
        'Alimentos' => [
            ['Cestas básicas para famílias atendidas', [20, 80]],
            ['Leite em pó para as crianças pequenas', [30, 100]],
            ['Alimentos para a sopa comunitária', [50, 200]],
            ['Frutas e verduras para o lanche', [20, 60]],
        ],
        'Materiais' => [
            ['Material escolar para o reforço', [20, 60]],
            ['Cadeiras para a sala de atividades', [10, 30]],
            ['Tintas e pincéis para a oficina de artes', [10, 40]],
            ['Computadores usados para o laboratório', [3, 10]],
            ['Bolas e coletes para os treinos', [10, 30]],
        ],
        'Voluntariado' => [
            ['Voluntários para aulas de reforço', [2, 6]],
            ['Voluntários para o mutirão de limpeza', [10, 30]],
            ['Contador voluntário para a prestação de contas', [1, 1]],
            ['Fotógrafo voluntário para o evento de fim de ano', [1, 2]],
            ['Voluntários para a triagem de doações', [4, 10]],
        ],
        'Vestuário' => [
            ['Agasalhos para a campanha de inverno', [30, 100]],
            ['Uniformes para o time', [15, 30]],
            ['Roupas infantis de 2 a 10 anos', [40, 120]],
            ['Calçados para adolescentes', [20, 50]],
        ],
        'Higiene' => [
            ['Kits de higiene pessoal', [30, 100]],
            ['Fraldas geriátricas', [50, 200]],
            ['Fraldas infantis tamanho G', [50, 150]],
            ['Produtos de limpeza para a sede', [10, 40]],
        ],
        'Educação' => [
            ['Livros infantis para a biblioteca', [30, 150]],
            ['Jogos pedagógicos para a educação infantil', [10, 30]],
            ['Apostilas para o pré-vestibular', [40, 100]],
            ['Bolsas para curso profissionalizante', [5, 15]],
        ],
        'Saúde' => [
            ['Cadeira de rodas para atendido', [1, 3]],
            ['Kits de primeiros socorros', [5, 15]],
            ['Psicólogo voluntário para atendimentos', [1, 2]],
            ['Óculos para alunos com baixa visão', [5, 20]],
        ],
        'Outros' => [
            ['Transporte para passeio educativo', [1, 2]],
            ['Botijões de gás para a cozinha', [4, 12]],
            ['Brinquedos para o Dia das Crianças', [50, 150]],
            ['Reforma do telhado da sede', [1, 1]],
        ],
    ];

    private const DETALHES = [
        'A doação pode ser entregue na sede, de segunda a sexta, das 8h às 17h.',
        'Atendemos hoje cerca de :pessoas pessoas e a procura aumentou neste semestre.',
        'Qualquer quantidade ajuda; combinamos a retirada se for preciso.',
        'Os itens serão usados nas atividades do próximo mês.',
    ];

    private const DETALHES_VOLUNTARIADO = [
        'Buscamos pessoas com disponibilidade de algumas horas por semana.',
        'Atendemos hoje cerca de :pessoas pessoas e precisamos de mais mãos.',
        'Oferecemos declaração de horas para estudantes.',
    ];

    /** Dias, depois da publicação, em que as manifestações costumam chegar. */
    private const DIAS_PARA_INTERESSE = 45;

    private const MENSAGENS = [
        'Posso ajudar a partir da próxima semana.',
        'Tenho alguns itens em casa, como faço para entregar?',
        'Trabalho perto da sede e posso passar no sábado.',
        'Gostaria de saber mais detalhes antes de doar.',
        'Sou estudante e preciso de horas complementares, posso ajudar?',
    ];

    private CarbonImmutable $agora;

    public function run(): void
    {
        fake()->seed(self::SEMENTE);
        $this->agora = CarbonImmutable::now();

        $this->call(AdmSeeder::class);
        Adm::firstOrCreate(
            ['email' => 'coordenacao@uoscc.org'],
            ['nome' => 'Coordenação da Rede', 'senha_hash' => Hash::make(self::SENHA_ADMINS)],
        );

        foreach (self::OSCS_POR_ADMIN as $emailAdmin => $oscs) {
            $admin = Adm::where('email', $emailAdmin)->firstOrFail();

            foreach ($oscs as $dados) {
                $this->criarOsc($admin, ...$dados);
            }
        }
    }

    private function criarOsc(Adm $admin, string $nome, string $area, string $bairro, string $descricao, ?string $situacao = null): void
    {
        // Cadastradas ao longo do último ano; algumas nas últimas semanas.
        $cadastro = $this->agora->subDays(fake()->numberBetween(5, 330))->setTime(fake()->numberBetween(8, 17), fake()->numberBetween(0, 59));

        $osc = Osc::factory()->for($admin, 'adm')->create([
            'nome' => $nome,
            'area_atuacao' => $area,
            'descricao' => $descricao,
            'endereco' => "Bairro {$bairro}, Camaçari - BA",
            'telefone' => sprintf('(71) 9%04d-%04d', fake()->numberBetween(8000, 9999), fake()->numberBetween(0, 9999)),
            'email' => Str::slug($nome, '.').'@example.org',
            'data_cadastro' => $cadastro,
        ]);

        $quantidade = match ($situacao) {
            self::SEM_DEMANDAS => 0,
            self::SO_CONCLUIDAS => 2,
            default => fake()->numberBetween(2, 5),
        };
        $categorias = self::CATEGORIAS_POR_AREA[$area];

        for ($i = 0; $i < $quantidade; $i++) {
            // A categoria principal da área aparece mais que as outras.
            $categoria = fake()->boolean(45) ? $categorias[0] : fake()->randomElement($categorias);
            $status = $situacao === self::SO_CONCLUIDAS
                ? Demanda::STATUS_CONCLUIDA
                : fake()->randomElement([...array_fill(0, 11, Demanda::STATUS_ABERTA), ...array_fill(0, 4, Demanda::STATUS_PARCIAL), ...array_fill(0, 5, Demanda::STATUS_CONCLUIDA)]);

            $this->criarDemanda($osc, $cadastro, $categoria, $status);
        }
    }

    private function criarDemanda(Osc $osc, CarbonImmutable $cadastroOsc, string $categoria, string $status): void
    {
        [$titulo, [$minimo, $maximo]] = fake()->randomElement(self::DEMANDAS[$categoria]);
        $ativa = $status !== Demanda::STATUS_CONCLUIDA;

        // Criadas nos últimos 6 meses, sempre depois do cadastro da OSC.
        $inicio = $cadastroOsc->max($this->agora->subMonths(6));
        $criacao = CarbonImmutable::createFromTimestamp(fake()->numberBetween($inicio->timestamp, $this->agora->subDay()->timestamp), $this->agora->timezone);

        $prazo = match (true) {
            ! $ativa => fake()->boolean(70) ? $criacao->addDays(fake()->numberBetween(15, 60)) : null,
            fake()->boolean(8) => $this->agora->subDays(fake()->numberBetween(1, 25)), // Vencida, ainda no feed.
            fake()->boolean(15) => null,
            default => $this->agora->addDays(fake()->numberBetween(5, 90)),
        };

        $demanda = Demanda::factory()->for($osc)->create([
            'titulo' => $titulo,
            'categoria' => $categoria,
            'descricao' => "{$titulo}. ".str_replace(':pessoas', (string) fake()->numberBetween(30, 250), fake()->randomElement(
                $categoria === 'Voluntariado' ? self::DETALHES_VOLUNTARIADO : self::DETALHES,
            )),
            'quantidade' => fake()->numberBetween($minimo, $maximo),
            'prazo' => $prazo?->toDateString(),
            'status' => $status,
            'urgencia' => $ativa && fake()->boolean(20),
            'data_criacao' => $criacao,
            'data_atualizacao' => $ativa ? $criacao : CarbonImmutable::createFromTimestamp(fake()->numberBetween($criacao->timestamp, $this->agora->timestamp), $this->agora->timezone),
        ]);

        // Demandas antigas e atendidas tendem a ter mais interessados; algumas ficam sem nenhum.
        $interessados = fake()->numberBetween($ativa ? 0 : 2, $ativa ? 6 : 8);
        $tipos = $categoria === 'Voluntariado'
            ? ['Voluntário', 'Voluntário', 'Voluntário', 'Aluno']
            : ['Apoiador/Doação', 'Apoiador/Doação', 'Apoiador/Doação', 'Voluntário', 'Aluno'];

        $ultimoInteresse = $criacao->addDays(self::DIAS_PARA_INTERESSE)->min($this->agora);

        for ($i = 0; $i < $interessados; $i++) {
            $nome = fake()->name();

            ManifestacaoInteresse::factory()->for($demanda)->create([
                'nome_apoiador' => $nome,
                'email' => Str::slug($nome, '.').".{$demanda->id_demanda}.{$i}@example.com",
                'telefone' => fake()->boolean(60) ? sprintf('(71) 9%04d-%04d', fake()->numberBetween(8000, 9999), fake()->numberBetween(0, 9999)) : null,
                'tipo_interesse' => fake()->randomElement($tipos),
                'mensagem' => fake()->boolean(50) ? fake()->randomElement(self::MENSAGENS) : null,
                'data_manifestacao' => CarbonImmutable::createFromTimestamp(fake()->numberBetween($criacao->timestamp, $ultimoInteresse->timestamp), $this->agora->timezone),
            ]);
        }
    }
}
