import { route } from 'ziggy-js';
import BotaoExcluir from '@/components/BotaoExcluir';
import CartaoPainel from '@/components/CartaoPainel';
import { EsqueletoLinhas } from '@/components/Esqueleto';
import Paginacao from '@/components/Paginacao';
import SeletorStatus from '@/components/SeletorStatus';
import { formatarDiaDoInstante } from '@/lib/formatar';
import type { Demanda, Paginado } from '@/types';

/** Moderação: todas as demandas da rede, paginadas no servidor (RF07). */
export default function TabelaDemandasRede({
    demandas,
    carregando,
}: {
    demandas: Paginado<Demanda>;
    carregando: boolean;
}) {
    return (
        <CartaoPainel className="mt-8">
            <div className="section-heading--compact">
                <div>
                    <span className="eyebrow">Moderação</span>
                    <h2>Demandas da rede</h2>
                </div>
                <span>{demandas.meta.total} no total</span>
            </div>
            {demandas.meta.total === 0 ? (
                <p className="empty-state">As demandas publicadas pelas OSCs aparecerão aqui.</p>
            ) : (
                <>
                    <div className="table-wrap" aria-busy={carregando}>
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Demanda</th>
                                    <th scope="col">OSC</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Interesses</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {carregando ? (
                                    <EsqueletoLinhas colunas={5} linhas={demandas.data.length} />
                                ) : (
                                    demandas.data.map((demanda) => (
                                        <tr key={demanda.id}>
                                            <td>
                                                <div className="data-table__title">
                                                    <strong>{demanda.titulo}</strong>
                                                    <small>
                                                        {demanda.categoria} · criada em{' '}
                                                        {formatarDiaDoInstante(demanda.criada_em)}
                                                        {demanda.urgente && (
                                                            <>
                                                                {' · '}
                                                                <span className="font-bold text-coral">Urgente</span>
                                                            </>
                                                        )}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>{demanda.osc?.nome}</td>
                                            <td>
                                                <SeletorStatus
                                                    demanda={demanda}
                                                    url={route('admin.demandas.status', demanda.id)}
                                                />
                                            </td>
                                            <td>{demanda.total_contatos ?? 0}</td>
                                            <td>
                                                <div className="table-actions">
                                                    {demanda.status !== 'Concluída' && (
                                                        <a
                                                            href={route('demandas.show', demanda.id)}
                                                            className="button button--secondary button--small"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                        >
                                                            Ver
                                                        </a>
                                                    )}
                                                    <BotaoExcluir
                                                        url={route('admin.demandas.destroy', demanda.id)}
                                                        titulo={`Excluir a demanda “${demanda.titulo}”?`}
                                                        descricao="As manifestações de interesse recebidas também serão apagadas. Esta ação não pode ser desfeita."
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    <Paginacao pagina={demandas} rotulo="Paginação das demandas" somente={['demandas']} />
                </>
            )}
        </CartaoPainel>
    );
}
