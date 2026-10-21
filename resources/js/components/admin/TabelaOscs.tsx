import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import BotaoExcluir from '@/components/BotaoExcluir';
import CartaoPainel from '@/components/CartaoPainel';
import { EsqueletoLinhas } from '@/components/Esqueleto';
import Icone from '@/components/Icone';
import Paginacao from '@/components/Paginacao';
import type { Osc, Paginado } from '@/types';

/** OSCs da rede, paginadas no servidor (RF09). */
export default function TabelaOscs({ oscs, carregando }: { oscs: Paginado<Osc>; carregando: boolean }) {
    return (
        <CartaoPainel>
            <div className="section-heading--compact">
                <div>
                    <span className="eyebrow">Rede</span>
                    <h2>OSCs cadastradas</h2>
                </div>
                <span>{oscs.meta.total} no total</span>
            </div>
            {oscs.meta.total === 0 ? (
                <p className="empty-state">Ainda não há OSCs cadastradas.</p>
            ) : (
                <>
                    <div className="table-wrap" aria-busy={carregando}>
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Organização</th>
                                    <th scope="col">Área</th>
                                    <th scope="col">Demandas</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {carregando ? (
                                    <EsqueletoLinhas colunas={4} linhas={oscs.data.length} />
                                ) : (
                                    oscs.data.map((osc) => (
                                        <tr key={osc.id}>
                                            <td>
                                                <div className="data-table__title">
                                                    <strong>{osc.nome}</strong>
                                                    <small>{osc.email}</small>
                                                </div>
                                            </td>
                                            <td>{osc.area_atuacao}</td>
                                            <td>{osc.total_demandas ?? 0}</td>
                                            <td>
                                                <div className="table-actions">
                                                    <Link
                                                        href={route('admin.oscs.edit', osc.id)}
                                                        className="button button--secondary button--small"
                                                    >
                                                        <Icone nome="edit" tamanho={15} /> Editar
                                                    </Link>
                                                    <BotaoExcluir
                                                        url={route('admin.oscs.destroy', osc.id)}
                                                        titulo={`Excluir “${osc.nome}”?`}
                                                        descricao="Todas as demandas da organização e as manifestações recebidas também serão apagadas. Esta ação não pode ser desfeita."
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    <Paginacao pagina={oscs} rotulo="Paginação das OSCs" somente={['oscs']} />
                </>
            )}
        </CartaoPainel>
    );
}
