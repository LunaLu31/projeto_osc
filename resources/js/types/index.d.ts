/*
 * Tipos do domínio. Derivam de `api.d.ts`, gerado a partir do OpenAPI da API
 * (`npm run tipos:api`), que por sua vez é gerado dos API Resources do Laravel
 * (app/Http/Resources/V1). O site (props do Inertia) usa os mesmos Resources,
 * então uma mudança no back-end quebra a checagem de tipos aqui.
 */
import type { Config } from 'ziggy-js';
import type { components, operations } from './api';

type Esquemas = components['schemas'];
type Json<O extends keyof operations> = operations[O]['responses'][200]['content']['application/json'];

export type Demanda = Esquemas['DemandaResource'];
export type OscPublica = Esquemas['OscPublicaResource'];
export type Osc = Esquemas['OscResource'];
export type Contato = Esquemas['ContatoResource'];

export type StatusDemanda = Demanda['status'];
export type TipoInteresse = Contato['tipo_interesse'];

export type MetricasRede = Json<'v1.rede.estatisticas'>['data'];
export type MetricasOsc = Json<'v1.osc.painel'>['data'];

/** Lista paginada no formato dos ResourceCollections do Laravel (data, links e meta). */
export type Paginado<T> = Omit<Json<'v1.demandas.index'>, 'data'> & { data: T[] };

export type Tema = 'sistema' | 'claro' | 'escuro';

export interface UsuarioLogado {
    id: number;
    nome: string;
    iniciais: string;
}

/** Props presentes em todas as páginas (app/Http/Middleware/HandleInertiaRequests.php). */
export interface PropsCompartilhadas {
    app: { nome: string };
    auth: { osc: UsuarioLogado | null; admin: UsuarioLogado | null };
    flash: { status: string | null; erro: string | null; senhaInicial: string | null };
    /** Preferência de tema (cookie `tema`), aplicada em data-theme no <html>. */
    tema: Tema;
    referencias: {
        categorias: string[];
        status: Record<StatusDemanda, string>;
        statusPublicos: StatusDemanda[];
        tiposInteresse: TipoInteresse[];
    };
    ziggy: Config;
    /** Erros de validação do Laravel (422), um por campo. */
    errors: Record<string, string>;
    [chave: string]: unknown;
}
