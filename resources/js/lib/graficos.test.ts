import { describe, expect, it } from 'vitest';
import { escalaLimpa, formatarMes } from './graficos';

describe('formatarMes', () => {
    it('formata o mês de calendário sem deslocar pelo fuso', () => {
        expect(formatarMes('2026-05')).toBe('mai.');
        expect(formatarMes('2026-01', true)).toBe('janeiro de 2026');
        expect(formatarMes('2026-12', true)).toBe('dezembro de 2026');
    });
});

describe('escalaLimpa', () => {
    it('usa marcas inteiras e redondas que alcançam o maior valor', () => {
        expect(escalaLimpa(1)).toEqual({ teto: 1, marcas: [0, 1] });
        expect(escalaLimpa(3)).toEqual({ teto: 3, marcas: [0, 1, 2, 3] });
        expect(escalaLimpa(7)).toEqual({ teto: 8, marcas: [0, 2, 4, 6, 8] });
        expect(escalaLimpa(37)).toEqual({ teto: 40, marcas: [0, 10, 20, 30, 40] });
        expect(escalaLimpa(1234).teto).toBe(1500);
    });

    it('não divide por zero quando não há dados', () => {
        expect(escalaLimpa(0)).toEqual({ teto: 1, marcas: [0, 1] });
    });
});
