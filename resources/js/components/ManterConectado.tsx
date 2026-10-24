/** Opção "Manter conectado" dos logins: o JWT vale por 7 dias mesmo fechando o navegador. */
export default function ManterConectado({
    marcado,
    alterar,
}: {
    marcado: boolean;
    alterar: (marcado: boolean) => void;
}) {
    return (
        <label className="checkbox-field">
            <input type="checkbox" checked={marcado} onChange={(e) => alterar(e.target.checked)} />
            <span>
                <strong>Manter conectado</strong>
                <br />
                Por 7 dias neste dispositivo. Não marque em computadores compartilhados.
            </span>
        </label>
    );
}
