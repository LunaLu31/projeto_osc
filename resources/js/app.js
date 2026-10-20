/*
 * Redcam – comportamentos de interface.
 * As páginas funcionam sem JavaScript; estes módulos apenas melhoram a experiência.
 */
import { iniciarMenu } from './menu';
import { iniciarAutoEnvio } from './filtros';
import { iniciarFormularios } from './formularios';

document.documentElement.classList.add('js');

iniciarMenu();
iniciarAutoEnvio();
iniciarFormularios();
