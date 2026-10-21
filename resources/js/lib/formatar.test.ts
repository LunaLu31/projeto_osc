import { describe, expect, it } from 'vitest';
import { apenasTelefone, formatarData, formatarDataHora, formatarDiaDoInstante, plural, resumir } from './formatar';

describe('formatar', () => {
    it('formata datas de calendário sem conversão de fuso', () => {
        expect(formatarData('2026-11-01')).toBe('01/11/2026');
        expect(formatarData(null)).toBeNull();
    });

    it('formata instantes no fuso de Camaçari (UTC-3), não no da máquina', () => {
        // 02:30 UTC do dia 21 ainda é dia 20 em Camaçari.
        expect(formatarDataHora('2026-10-21T02:30:00Z')).toBe('20/10/2026 23:30');
        expect(formatarDataHora('2026-10-21T02:30:00Z', true)).toBe('20/10 23:30');
        expect(formatarDiaDoInstante('2026-10-21T02:30:00Z')).toBe('20/10/2026');
        expect(formatarDataHora(null)).toBe('—');
    });

    it('pluraliza, resume e limpa telefones', () => {
        expect(plural(1, 'contato', 'contatos')).toBe('1 contato');
        expect(plural(3, 'contato', 'contatos')).toBe('3 contatos');
        expect(resumir('  texto   com   espaços  ')).toBe('texto com espaços');
        expect(resumir('a'.repeat(200), 10)).toBe('aaaaaaaaa…');
        expect(apenasTelefone('(71) 99999-0000')).toBe('71999990000');
    });
});
