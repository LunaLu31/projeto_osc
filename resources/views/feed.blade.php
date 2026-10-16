<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Redcam | Rede que transforma</title>

    <!-- Alpine.js para reatividade (pode ser instalado via npm se preferir) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS (para utilitários como size-4, size-5, etc, caso não estejam no seu CSS) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Utilitários de tamanho para os ícones */
        .size-4 {
            width: 1rem;
            height: 1rem;
        }

        .size-5 {
            width: 1.25rem;
            height: 1.25rem;
        }

        .size-7 {
            width: 1.75rem;
            height: 1.75rem;
        }

        .size-8 {
            width: 2rem;
            height: 2rem;
        }

        /* Previne flash de conteúdo não renderizado pelo Alpine */
        [x-cloak] {
            display: none !important;
        }

        /* === COLE AQUI O SEU CSS FORNECIDO === */
        @import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap");

        :root {
            --ink: #18304b;
            --muted: #5e7188;
            --primary: #347db8;
            --primary-dark: #245d8d;
            --primary-light: #e2f1fc;
            --lime: #b9e1fa;
            --coral: #e55f46;
            --coral-light: #fff0ec;
            --blue-light: #eaf2ff;
            --surface: #ffffff;
            --canvas: #f5f9fd;
            --line: #dce8f2;
            --shadow: 0 22px 60px rgba(28, 66, 103, 0.1);
            color: var(--ink);
            font-family: "DM Sans", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-width: 320px;
            min-height: 100vh;
            background: var(--canvas);
        }

        button,
        input,
        select,
        textarea {
            color: inherit;
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid rgba(52, 125, 184, 0.24);
            outline-offset: 2px;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1,
        h2,
        h3,
        .logo strong {
            font-family: "Manrope", sans-serif;
        }

        .site-shell {
            min-height: 100vh;
            overflow: hidden;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 82px;
            padding: 0 max(5vw, 24px);
            border-bottom: 1px solid rgba(220, 232, 242, 0.88);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(18px);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            padding: 0;
            border: 0;
            background: transparent;
            text-align: left;
        }

        .logo__mark {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 14px 14px 14px 5px;
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 20px rgba(52, 125, 184, 0.2);
        }

        .logo__copy {
            display: flex;
            flex-direction: column;
        }

        .logo__copy strong {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .logo__copy small {
            margin-top: -1px;
            color: var(--muted);
            font-size: 10px;
            letter-spacing: 0.025em;
        }

        .navigation-menu {
            position: relative;
        }

        .nav {
            position: absolute;
            top: calc(100% + 12px);
            left: 0;
            z-index: 50;
            display: flex;
            min-width: 240px;
            flex-direction: column;
            gap: 2px;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: white;
            box-shadow: 0 16px 40px rgba(28, 66, 103, 0.14);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease;
        }

        .navigation-menu:hover .nav,
        .navigation-menu:focus-within .nav,
        .nav--open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav button,
        .nav a {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 9px;
            padding: 11px 12px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: var(--muted);
            font-size: 14px;
            text-align: left;
            text-decoration: none;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav button:hover,
        .nav a:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .site-footer button,
        .dashboard-list .section-heading button {
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 14px;
            transition: color 180ms ease;
        }

        .site-footer button:hover {
            color: var(--primary);
        }

        .button {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 19px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            transition: transform 180ms ease, background 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button--primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 9px 20px rgba(52, 125, 184, 0.17);
        }

        .button--primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 12px 24px rgba(52, 125, 184, 0.25);
        }

        .button--secondary {
            border-color: var(--line);
            background: white;
            color: var(--ink);
        }

        .button--secondary:hover {
            border-color: #a9bfd1;
            background: #f8fbfe;
        }

        .button--ghost {
            background: transparent;
            color: var(--muted);
        }

        .button--light {
            background: white;
            color: var(--primary-dark);
            box-shadow: 0 10px 25px rgba(29, 66, 103, 0.15);
        }

        .menu-button {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: white;
        }

        .hero {
            position: relative;
            display: grid;
            min-height: 660px;
            grid-template-columns: 1.04fr 0.96fr;
            align-items: center;
            gap: 7vw;
            padding: 72px max(7vw, 24px) 82px;
            background: radial-gradient(circle at 12% 18%, rgba(167, 218, 250, 0.3), transparent 27%), linear-gradient(118deg, #eaf6ff 0%, #f8fbff 54%, #e3f1fc 100%);
        }

        .hero::before {
            position: absolute;
            right: 42%;
            bottom: 6%;
            width: 110px;
            height: 110px;
            border: 1px solid rgba(52, 125, 184, 0.18);
            border-radius: 50%;
            content: "";
        }

        .hero__content {
            position: relative;
            z-index: 1;
            max-width: 650px;
        }

        .hero__eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .hero__eyebrow span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .hero__eyebrow span:first-child {
            padding: 8px 11px;
            border-radius: 999px;
            background: var(--primary-light);
        }

        .hero__eyebrow svg {
            width: 15px;
            height: 15px;
        }

        .hero h1 {
            max-width: 700px;
            margin-bottom: 24px;
            color: var(--ink);
            font-size: clamp(42px, 5.2vw, 72px);
            font-weight: 700;
            letter-spacing: -0.055em;
            line-height: 1.04;
        }

        .hero h1 em {
            position: relative;
            color: var(--primary);
            font-style: normal;
        }

        .hero h1 em::after {
            position: absolute;
            right: 0;
            bottom: -7px;
            left: 0;
            height: 8px;
            border-radius: 50%;
            background: var(--lime);
            content: "";
            transform: rotate(-1deg);
            z-index: -1;
        }

        .hero__content>p {
            max-width: 560px;
            margin-bottom: 31px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.72;
        }

        .hero__actions {
            display: flex;
            gap: 12px;
        }

        .hero__actions .button {
            min-height: 52px;
            padding: 0 24px;
        }

        .hero__trust {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 34px;
            color: var(--muted);
            font-size: 12px;
        }

        .hero__trust>span:last-child {
            display: flex;
            flex-direction: column;
        }

        .hero__trust strong {
            color: var(--ink);
            font-size: 13px;
        }

        .avatar-stack {
            display: flex;
            padding-left: 8px;
        }

        .avatar-stack i {
            display: grid;
            width: 34px;
            height: 34px;
            margin-left: -8px;
            place-items: center;
            border: 2px solid white;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 9px;
            font-style: normal;
            font-weight: 800;
        }

        .avatar-stack i:nth-child(2) {
            background: #fff0ce;
        }

        .avatar-stack i:nth-child(3) {
            background: #e5f0ff;
        }

        .hero__visual {
            position: relative;
            height: 530px;
        }

        .hero__visual::before {
            position: absolute;
            inset: -18px 16px 18px -18px;
            border: 1px solid rgba(52, 125, 184, 0.18);
            border-radius: 40% 18px 40% 18px;
            content: "";
            transform: rotate(-2deg);
        }

        .hero__visual>img {
            width: 100%;
            height: 100%;
            border-radius: 42% 18px 42% 18px;
            object-fit: cover;
            filter: saturate(0.85) contrast(0.95);
            box-shadow: var(--shadow);
        }

        .hero__visual::after {
            position: absolute;
            inset: 0;
            border-radius: 42% 18px 42% 18px;
            background: linear-gradient(180deg, transparent 55%, rgba(22, 61, 99, 0.24));
            content: "";
        }

        .hero__impact-card,
        .hero__verified-card {
            position: absolute;
            z-index: 2;
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 16px 45px rgba(28, 66, 103, 0.18);
            backdrop-filter: blur(14px);
        }

        .hero__impact-card {
            right: -32px;
            bottom: 48px;
            gap: 12px;
            min-width: 255px;
            padding: 15px;
            border-radius: 16px;
        }

        .impact-icon {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 12px;
            background: var(--coral-light);
            color: var(--coral);
        }

        .hero__impact-card div,
        .hero__verified-card span {
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .hero__impact-card strong,
        .hero__verified-card strong {
            font-size: 13px;
        }

        .hero__impact-card div span,
        .hero__verified-card span span {
            color: var(--muted);
            font-size: 11px;
        }

        .impact-growth {
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
        }

        .hero__verified-card {
            top: 45px;
            left: -28px;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 14px;
            color: var(--primary);
        }

        .hero__verified-card>svg {
            width: 22px;
        }

        .quick-stats {
            position: relative;
            z-index: 4;
            display: grid;
            max-width: 1160px;
            grid-template-columns: repeat(4, 1fr);
            margin: -34px auto 0;
            padding: 25px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: white;
            box-shadow: var(--shadow);
        }

        .quick-stats article {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 1px solid var(--line);
        }

        .quick-stats article:last-child {
            border: 0;
        }

        .quick-stats strong {
            color: var(--primary);
            font-family: "Manrope", sans-serif;
            font-size: 26px;
        }

        .quick-stats span {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
        }

        .demands-section,
        .how-section,
        .about-section {
            padding: 112px max(7vw, 24px);
        }

        .section-heading {
            display: flex;
            max-width: 1240px;
            align-items: flex-end;
            justify-content: space-between;
            margin: 0 auto 38px;
        }

        .section-heading h2,
        .about-section h2,
        .cta-section h2 {
            margin: 7px 0 10px;
            color: var(--ink);
            font-size: clamp(30px, 3.4vw, 44px);
            font-weight: 700;
            letter-spacing: -0.045em;
            line-height: 1.15;
        }

        .section-heading p {
            margin: 0;
            color: var(--muted);
        }

        .eyebrow {
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .section-counter {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
        }

        .section-counter strong {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 11px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 15px;
        }

        .filters {
            display: grid;
            max-width: 1240px;
            grid-template-columns: minmax(280px, 1fr) 190px auto;
            gap: 12px;
            margin: 0 auto 28px;
        }

        .search-box {
            position: relative;
        }

        .search-box svg {
            position: absolute;
            top: 15px;
            left: 16px;
            width: 19px;
            color: var(--muted);
        }

        .filters input,
        .filters select,
        .urgent-toggle {
            width: 100%;
            height: 50px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: white;
        }

        .filters input {
            padding: 0 18px 0 47px;
        }

        .filters select {
            padding: 0 15px;
        }

        .urgent-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 17px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .urgent-toggle svg {
            width: 18px;
        }

        .urgent-toggle--active {
            border-color: #f1ad9f;
            background: var(--coral-light);
            color: var(--coral);
        }

        .demand-grid {
            display: grid;
            max-width: 1240px;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 0 auto;
        }

        .demand-card {
            display: flex;
            min-width: 0;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: white;
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }

        .demand-card:hover {
            border-color: #bdd2e4;
            box-shadow: 0 22px 50px rgba(28, 66, 103, 0.09);
            transform: translateY(-4px);
        }

        .demand-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 0;
        }

        .org-avatar {
            display: grid;
            width: 42px;
            height: 42px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-family: "Manrope", sans-serif;
            font-size: 12px;
            font-weight: 800;
        }

        .org-avatar--large {
            width: 62px;
            height: 62px;
            margin-top: 34px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.17);
            color: white;
            font-size: 17px;
        }

        .demand-card__badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 9px;
            border-radius: 999px;
            background: #eff2f1;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .badge--urgent {
            background: var(--coral-light);
            color: var(--coral);
        }

        .badge--success {
            background: var(--primary-light);
            color: var(--primary);
        }

        .badge--blue {
            background: var(--blue-light);
            color: #456ba1;
        }

        .demand-card__body {
            flex: 1;
            padding: 18px 20px 20px;
        }

        .demand-card h3 {
            margin: 8px 0 5px;
            font-size: 19px;
            letter-spacing: -0.025em;
            line-height: 1.32;
        }

        .demand-card__org {
            margin-bottom: 14px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }

        .demand-card__description {
            min-height: 62px;
            margin-bottom: 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .demand-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            padding: 13px 0;
            border-top: 1px solid #e8eef4;
            border-bottom: 1px solid #e8eef4;
            color: var(--muted);
            font-size: 11px;
        }

        .demand-card__meta span,
        .demand-card__footer>span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .demand-card__meta svg,
        .demand-card__footer>span svg {
            width: 15px;
            height: 15px;
            color: var(--primary);
        }

        .progress-copy {
            display: flex;
            justify-content: space-between;
            margin: 17px 0 8px;
            color: var(--muted);
            font-size: 10px;
        }

        .progress-copy strong {
            color: var(--primary);
        }

        .progress {
            height: 5px;
            overflow: hidden;
            border-radius: 99px;
            background: #e8eff5;
        }

        .progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--primary);
        }

        .demand-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #f8fbfe;
            color: var(--muted);
            font-size: 10px;
        }

        .demand-card__footer .button {
            min-height: 37px;
            padding: 0 13px;
            font-size: 11px;
        }

        .empty-state {
            max-width: 1240px;
            margin: 0 auto;
            padding: 70px 24px;
            border: 1px dashed #bdd2e4;
            border-radius: 18px;
            background: white;
            text-align: center;
        }

        .empty-state>svg {
            width: 36px;
            margin: 0 auto 15px;
            color: var(--primary);
        }

        .empty-state h3 {
            margin-bottom: 7px;
        }

        .empty-state p {
            color: var(--muted);
        }

        .how-section {
            background: #edf6fd;
        }

        .section-heading--center {
            justify-content: center;
            text-align: center;
        }

        .steps {
            display: grid;
            max-width: 1100px;
            grid-template-columns: repeat(3, 1fr);
            gap: 52px;
            margin: 0 auto;
        }

        .steps article {
            position: relative;
            text-align: center;
        }

        .steps article:not(:last-child)::after {
            position: absolute;
            top: 39px;
            right: -44px;
            width: 70px;
            border-top: 1px dashed #9dbbd4;
            content: "";
        }

        .step-number {
            position: absolute;
            top: -7px;
            left: calc(50% + 32px);
            color: #91aec8;
            font-family: "Manrope", sans-serif;
            font-size: 11px;
            font-weight: 800;
        }

        .step-icon {
            display: grid;
            width: 76px;
            height: 76px;
            margin: 0 auto 22px;
            place-items: center;
            border: 1px solid #c8deef;
            border-radius: 24px;
            background: white;
            color: var(--primary);
            box-shadow: 0 12px 30px rgba(52, 125, 184, 0.09);
        }

        .step-icon svg {
            width: 29px;
            height: 29px;
        }

        .steps h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .steps p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .about-section {
            display: grid;
            max-width: 1280px;
            grid-template-columns: 1fr 0.8fr;
            align-items: center;
            gap: 100px;
            margin: 0 auto;
        }

        .about-section__copy>p {
            color: var(--muted);
            line-height: 1.75;
        }

        .about-section ul {
            display: grid;
            gap: 13px;
            margin: 26px 0 0;
            padding: 0;
            list-style: none;
        }

        .about-section li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .about-section li svg {
            width: 20px;
            color: var(--primary);
        }

        .about-section__quote {
            position: relative;
            padding: 55px;
            border-radius: 32px 32px 32px 8px;
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow);
        }

        .about-section__quote>svg {
            margin-bottom: 26px;
            color: var(--lime);
        }

        .about-section blockquote {
            margin: 0 0 24px;
            font-family: "Manrope", sans-serif;
            font-size: 25px;
            font-weight: 600;
            letter-spacing: -0.025em;
            line-height: 1.45;
        }

        .about-section__quote p {
            margin: 0;
            color: rgba(255, 255, 255, 0.66);
            font-size: 12px;
        }

        .cta-section {
            margin: 0 max(5vw, 20px) 80px;
            padding: 76px 24px;
            border-radius: 32px;
            background: radial-gradient(circle at 84% 16%, rgba(130, 196, 238, 0.24), transparent 25%), var(--primary-dark);
            color: white;
            text-align: center;
        }

        .cta-section .eyebrow {
            color: var(--lime);
        }

        .cta-section h2 {
            margin: 9px auto 12px;
            color: white;
        }

        .cta-section p {
            max-width: 570px;
            margin: 0 auto 28px;
            color: rgba(255, 255, 255, 0.67);
            line-height: 1.7;
        }

        .site-footer {
            display: grid;
            grid-template-columns: 1.5fr 0.7fr 0.8fr 1fr;
            gap: 60px;
            padding: 70px max(7vw, 24px) 50px;
            border-top: 1px solid var(--line);
            background: white;
        }

        .site-footer>div {
            display: flex;
            align-items: flex-start;
            flex-direction: column;
            gap: 13px;
        }

        .site-footer>div:first-child p {
            max-width: 280px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .site-footer>div>strong {
            margin-bottom: 5px;
            font-size: 13px;
        }

        .footer-signature {
            padding: 20px;
            border-radius: 16px;
            background: var(--primary-light);
        }

        .footer-signature strong {
            color: var(--primary);
        }

        .footer-signature span {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            padding: 18px max(7vw, 24px);
            border-top: 1px solid var(--line);
            background: white;
            color: var(--muted);
            font-size: 10px;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            overflow-y: auto;
            place-items: center;
            padding: 32px 20px;
            background: rgba(18, 42, 67, 0.66);
            backdrop-filter: blur(7px);
            animation: fade-in 180ms ease;
        }

        .modal {
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 22px;
            background: white;
            box-shadow: 0 35px 90px rgba(12, 32, 54, 0.3);
            animation: rise-in 240ms ease;
        }

        .modal--large {
            max-width: 900px;
        }

        .modal-close {
            position: fixed;
            top: 24px;
            right: 25px;
            z-index: 102;
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.13);
            color: white;
        }

        .detail-layout {
            display: grid;
            grid-template-columns: 0.75fr 1.25fr;
        }

        .detail-aside {
            padding: 46px 38px;
            background: radial-gradient(circle at 15% 10%, rgba(130, 196, 238, 0.18), transparent 32%), var(--primary-dark);
            color: white;
        }

        .detail-aside .eyebrow {
            color: var(--lime);
        }

        .detail-aside h2 {
            margin: 15px 0 10px;
            font-size: 23px;
        }

        .detail-aside>p {
            color: rgba(255, 255, 255, 0.62);
            font-size: 13px;
            line-height: 1.65;
        }

        .verified {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 35px;
            color: var(--lime);
            font-size: 11px;
            font-weight: 700;
        }

        .verified svg {
            width: 18px;
        }

        .detail-content {
            padding: 46px;
        }

        .detail-content>.eyebrow {
            display: block;
            margin-top: 25px;
        }

        .detail-content h2,
        .interest-form h2,
        .login-form h2,
        .success-state h2 {
            margin: 8px 0 10px;
            color: var(--ink);
            font-size: 27px;
            letter-spacing: -0.035em;
        }

        .detail-description,
        .interest-form>div:first-child p,
        .login-form>p,
        .success-state>p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin: 24px 0;
        }

        .detail-grid>div {
            display: flex;
            min-width: 0;
            flex-direction: column;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        .detail-grid svg {
            width: 18px;
            margin-bottom: 13px;
            color: var(--primary);
        }

        .detail-grid span {
            color: var(--muted);
            font-size: 9px;
            text-transform: uppercase;
        }

        .detail-grid strong {
            margin-top: 4px;
            overflow: hidden;
            font-size: 11px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .privacy-note {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0;
            padding: 13px;
            border-radius: 11px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 11px;
            line-height: 1.4;
        }

        .privacy-note svg {
            width: 20px;
            flex: 0 0 auto;
        }

        .interest-form,
        .login-form,
        .success-state {
            padding: 42px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-top: 15px;
        }

        .field__label {
            font-size: 11px;
            font-weight: 800;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: #f8fbfe;
        }

        .field input,
        .field select {
            height: 47px;
            padding: 0 13px;
        }

        .field textarea {
            padding: 13px;
            resize: vertical;
        }

        .field__hint {
            color: var(--muted);
            font-size: 10px;
        }

        .form-error {
            margin: 12px 0 0;
            padding: 10px 12px;
            border-radius: 9px;
            background: var(--coral-light);
            color: #a33f2d;
            font-size: 11px;
            font-weight: 600;
        }

        .consent {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin: 17px 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.5;
        }

        .consent input {
            margin-top: 2px;
            accent-color: var(--primary);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
        }

        .success-state {
            text-align: center;
        }

        .success-state__icon {
            display: grid;
            width: 68px;
            height: 68px;
            margin: 0 auto 25px;
            place-items: center;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
        }

        .success-state .privacy-note {
            justify-content: center;
            text-align: left;
        }

        .login-form {
            text-align: center;
        }

        .login-form .field {
            text-align: left;
        }

        .login-icon {
            display: grid;
            width: 56px;
            height: 56px;
            margin: 0 auto 23px;
            place-items: center;
            border-radius: 17px;
            background: var(--primary-light);
            color: var(--primary);
        }

        .login-form .button {
            margin-top: 22px;
        }

        .login-setup-link {
            display: inline-block;
            margin-top: 16px;
            color: var(--muted);
            font-size: 11px;
            text-decoration: none;
        }

        .login-setup-link:hover {
            color: var(--primary);
        }

        .demo-note {
            margin: 13px 0 0 !important;
            font-size: 10px !important;
        }

        .dashboard {
            min-height: 100vh;
            background: var(--canvas);
        }

        .dashboard-header {
            display: flex;
            min-height: 78px;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(5vw, 24px);
            border-bottom: 1px solid var(--line);
            background: white;
        }

        .dashboard-user {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .dashboard-user>span {
            display: flex;
            flex-direction: column;
            font-size: 12px;
        }

        .dashboard-user small {
            color: var(--muted);
        }

        .dashboard-main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 62px 24px 100px;
        }

        .dashboard-title {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .dashboard-title h1 {
            margin: 7px 0;
            font-size: clamp(29px, 4vw, 40px);
            letter-spacing: -0.045em;
        }

        .dashboard-title p {
            margin: 0;
            color: var(--muted);
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 35px 0;
        }

        .metric-grid article {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 21px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: white;
        }

        .metric-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            border-radius: 14px;
        }

        .metric-icon--blue {
            background: var(--blue-light);
            color: #456ba1;
        }

        .metric-icon--green {
            background: var(--primary-light);
            color: var(--primary);
        }

        .metric-icon--orange {
            background: var(--coral-light);
            color: var(--coral);
        }

        .metric-grid article>div {
            display: flex;
            flex-direction: column;
        }

        .metric-grid article strong {
            font-family: "Manrope", sans-serif;
            font-size: 24px;
        }

        .metric-grid article span {
            color: var(--muted);
            font-size: 11px;
        }

        .dashboard-list {
            padding: 28px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: white;
        }

        .section-heading--compact {
            margin-bottom: 22px;
        }

        .section-heading--compact h2 {
            margin: 4px 0 0;
            font-size: 23px;
        }

        .dashboard-list .section-heading button {
            color: var(--primary);
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            padding: 11px 15px;
            background: #f2f7fb;
            color: var(--muted);
            font-size: 9px;
            letter-spacing: 0.06em;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid var(--line);
        }

        td:first-child {
            display: flex;
            min-width: 240px;
            flex-direction: column;
        }

        td:first-child span {
            margin-top: 3px;
            color: var(--muted);
            font-size: 10px;
        }

        .interest-count {
            color: var(--primary);
        }

        td .button {
            min-height: 34px;
            padding: 0 12px;
            font-size: 10px;
        }

        .w-full {
            width: 100%;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.985);
            }
        }

        @media (max-width: 1060px) {
            .hero {
                gap: 5vw;
            }

            .hero__visual {
                height: 460px;
            }

            .hero__impact-card {
                right: -10px;
            }

            .demand-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 780px) {
            .site-header {
                min-height: 70px;
            }

            .hero {
                min-height: auto;
                grid-template-columns: 1fr;
                padding-top: 54px;
            }

            .hero__content {
                max-width: none;
            }

            .hero__visual {
                height: 420px;
                margin: 20px 8px 0;
            }

            .hero__verified-card {
                left: -10px;
            }

            .quick-stats {
                width: calc(100% - 40px);
                grid-template-columns: repeat(2, 1fr);
                gap: 22px 0;
            }

            .quick-stats article:nth-child(2) {
                border: 0;
            }

            .section-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: 20px;
            }

            .filters {
                grid-template-columns: 1fr 1fr;
            }

            .filters .search-box {
                grid-column: 1 / -1;
            }

            .steps {
                grid-template-columns: 1fr;
                gap: 42px;
            }

            .steps article:not(:last-child)::after {
                display: none;
            }

            .steps p {
                max-width: 420px;
                margin: 0 auto;
            }

            .about-section {
                grid-template-columns: 1fr;
                gap: 45px;
            }

            .site-footer {
                grid-template-columns: 1fr 1fr;
            }

            .detail-layout {
                grid-template-columns: 1fr;
            }

            .detail-aside {
                padding: 35px;
            }

            .detail-aside .org-avatar,
            .detail-aside>p {
                display: none;
            }

            .verified {
                margin-top: 15px;
            }

            .metric-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .hero__eyebrow span:last-child {
                display: none;
            }

            .hero {
                padding-right: 20px;
                padding-left: 20px;
            }

            .hero h1 {
                font-size: 42px;
            }

            .hero__content>p {
                font-size: 16px;
            }

            .hero__actions {
                align-items: stretch;
                flex-direction: column;
            }

            .hero__visual {
                height: 340px;
            }

            .hero__verified-card {
                top: 20px;
            }

            .hero__impact-card {
                right: -4px;
                bottom: 22px;
                min-width: 225px;
            }

            .quick-stats {
                padding: 20px 10px;
            }

            .quick-stats span {
                font-size: 10px;
            }

            .demands-section,
            .how-section,
            .about-section {
                padding: 78px 20px;
            }

            .filters {
                grid-template-columns: 1fr;
            }

            .filters .search-box {
                grid-column: auto;
            }

            .demand-grid {
                grid-template-columns: 1fr;
            }

            .about-section__quote {
                padding: 38px 28px;
            }

            .cta-section {
                padding: 56px 22px;
            }

            .site-footer {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                align-items: flex-start;
                flex-direction: column;
                gap: 6px;
            }

            .modal-backdrop {
                align-items: end;
                padding: 0;
            }

            .modal {
                max-height: 92vh;
                overflow-y: auto;
                border-radius: 22px 22px 0 0;
            }

            .modal-close {
                top: 12px;
                right: 12px;
                width: 38px;
                height: 38px;
                border-color: var(--line);
                background: white;
                color: var(--ink);
            }

            .detail-aside {
                padding: 28px 25px;
            }

            .detail-aside h2 {
                padding-right: 40px;
            }

            .detail-content,
            .interest-form,
            .login-form,
            .success-state {
                padding: 28px 22px;
            }

            .detail-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .detail-grid {
                gap: 7px;
            }

            .detail-grid>div {
                display: grid;
                grid-template-columns: 24px 1fr;
            }

            .detail-grid svg {
                grid-row: 1 / 3;
                margin: 2px 0 0;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .button {
                width: 100%;
            }

            .dashboard-header .logo__copy {
                display: none;
            }

            .dashboard-user>span {
                display: none;
            }

            .dashboard-title {
                align-items: stretch;
                flex-direction: column;
                gap: 22px;
            }

            .dashboard-list {
                padding: 20px 15px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body x-data="appData()" x-cloak :class="{ 'overflow-hidden': showLogin || selectedDemand || showCreate }">

    <!-- ================= DASHBOARD VIEW ================= -->
    <template x-if="dashboard">
        <div class="dashboard">
            <header class="dashboard-header">
                <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="logo" type="button" aria-label="Ir para o início">
                    <span class="logo__mark" x-html="getIcon('heart', 'size-5')"></span>
                    <span class="logo__copy">
                        <strong>Redcam</strong>
                        <small>Rede que transforma</small>
                    </span>
                </button>
                <div class="dashboard-user">
                    <div class="org-avatar" x-text="authenticatedOsc?.initials"></div>
                    <span>
                        <strong x-text="authenticatedOsc?.nome"></strong>
                        <small>Perfil da OSC</small>
                    </span>
                    <button @click="submitLogout" class="button button--ghost">Sair</button>
                </div>
            </header>
            <main class="dashboard-main">
                <section class="dashboard-title">
                    <div>
                        <span class="eyebrow">Painel da organização</span>
                        <h1>Bem-vinda(o), <span x-text="authenticatedOsc?.nome"></span></h1>
                        <p>Acompanhe suas demandas e conexões em um só lugar.</p>
                    </div>
                    <button @click="showCreate = true" class="button button--primary">
                        <span x-html="getIcon('plus')"></span> Nova demanda
                    </button>
                </section>
                <section class="metric-grid">
                    <article>
                        <span class="metric-icon metric-icon--blue"><span x-html="getIcon('box')"></span></span>
                        <div><strong>4</strong><span>Demandas ativas</span></div>
                    </article>
                    <article>
                        <span class="metric-icon metric-icon--green"><span x-html="getIcon('people')"></span></span>
                        <div><strong>18</strong><span>Novos interessados</span></div>
                    </article>
                    <article>
                        <span class="metric-icon metric-icon--orange"><span x-html="getIcon('check')"></span></span>
                        <div><strong>12</strong><span>Demandas concluídas</span></div>
                    </article>
                </section>
                <section class="dashboard-list">
                    <div class="section-heading section-heading--compact">
                        <div>
                            <span class="eyebrow">Gestão</span>
                            <h2>Minhas demandas</h2>
                        </div>
                        <button type="button">Ver todas</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Demanda</th>
                                    <th>Status</th>
                                    <th>Prazo</th>
                                    <th>Interessados</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Alimentos para 40 famílias</strong>
                                        <span>Alimentos · 40 cestas</span>
                                    </td>
                                    <td><span class="badge badge--success">Aberta</span></td>
                                    <td>18 jun. 2025</td>
                                    <td><strong class="interest-count">9 contatos</strong></td>
                                    <td><button class="button button--secondary">Gerenciar</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Oficina de horta comunitária</strong>
                                        <span>Voluntariado · 5 pessoas</span>
                                    </td>
                                    <td><span class="badge badge--blue">Parcial</span></td>
                                    <td>28 jun. 2025</td>
                                    <td><strong class="interest-count">6 contatos</strong></td>
                                    <td><button class="button button--secondary">Gerenciar</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Materiais escolares</strong>
                                        <span>Educação · 30 kits</span>
                                    </td>
                                    <td><span class="badge">Concluída</span></td>
                                    <td>02 jun. 2025</td>
                                    <td><strong class="interest-count">3 contatos</strong></td>
                                    <td><button class="button button--secondary">Gerenciar</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Modal Criar Demanda -->
            <template x-if="showCreate">
                <div class="modal-backdrop" role="dialog" aria-modal="true" @click.self="showCreate = false">
                    <button @click="showCreate = false" class="modal-close" aria-label="Fechar janela">
                        <span x-html="getIcon('close')"></span>
                    </button>
                    <div class="modal">
                        <form class="interest-form" @submit.prevent="showCreate = false">
                            <div>
                                <span class="eyebrow">Nova publicação</span>
                                <h2>Criar demanda</h2>
                                <p>Descreva de forma clara o que sua organização precisa.</p>
                            </div>
                            <label class="field">
                                <span class="field__label">Título</span>
                                <input type="text" placeholder="Ex.: 30 kits de material escolar" required>
                            </label>
                            <div class="form-grid">
                                <label class="field">
                                    <span class="field__label">Categoria</span>
                                    <select required>
                                        <option>Alimentos</option>
                                        <option>Materiais</option>
                                        <option>Voluntariado</option>
                                        <option>Vestuário</option>
                                    </select>
                                </label>
                                <label class="field">
                                    <span class="field__label">Quantidade</span>
                                    <input type="number" min="1" placeholder="30" required>
                                </label>
                            </div>
                            <label class="field">
                                <span class="field__label">Descrição</span>
                                <textarea placeholder="Explique a necessidade..." required rows="3"></textarea>
                            </label>
                            <div class="form-actions">
                                <button type="button" @click="showCreate = false" class="button button--secondary">Cancelar</button>
                                <button type="submit" class="button button--primary">Publicar demanda</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- ================= MAIN SITE VIEW ================= -->
    <template x-if="!dashboard">
        <div class="site-shell">
            <header class="site-header">
                <div class="navigation-menu" @click.outside="menuOpen = false">
                    <button @click="menuOpen = !menuOpen" class="menu-button" aria-label="Abrir menu de navegação" aria-haspopup="true" :aria-expanded="menuOpen" type="button">
                        <span x-html="getIcon(menuOpen ? 'close' : 'menu')"></span>
                    </button>
                    <nav :class="menuOpen ? 'nav nav--open' : 'nav'" aria-label="Navegação principal">
                        <button @click="scrollToDemands" type="button">
                            <span x-html="getIcon('home')"></span> Feed
                        </button>
                        <button @click="scrollToHow" type="button">
                            <span x-html="getIcon('spark')"></span> Como funciona
                        </button>
                        <button @click="scrollToAbout" type="button">
                            <span x-html="getIcon('people')"></span> Sobre a rede
                        </button>
                        <button @click="openLogin('admin')" type="button">
                            <span x-html="getIcon('shield')"></span> Portal administrativo
                        </button>
                        <button @click="openLogin('osc')" type="button">
                            <span x-html="getIcon('user')"></span> Área da OSC
                        </button>
                    </nav>
                </div>
                <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="logo" type="button" aria-label="Ir para o início">
                    <span class="logo__mark" x-html="getIcon('heart', 'size-5')"></span>
                    <span class="logo__copy">
                        <strong>Redcam</strong>
                        <small>Rede que transforma</small>
                    </span>
                </button>
            </header>

            <main>
                <!-- Hero -->
                <section class="hero">
                    <div class="hero__content">
                        <div class="hero__eyebrow">
                            <span><span x-html="getIcon('location', 'size-4')"></span> Camaçari, Bahia</span>
                            <span>Conectando quem transforma</span>
                        </div>
                        <h1>Sua vontade de ajudar encontra <em>quem precisa.</em></h1>
                        <p>Descubra necessidades reais de organizações sociais de Camaçari e contribua com doações, tempo ou conhecimento. Sem burocracia.</p>
                        <div class="hero__actions">
                            <button @click="scrollToDemands" class="button button--primary">
                                Encontrar uma causa <span x-html="getIcon('arrow')"></span>
                            </button>
                            <button @click="scrollToHow" class="button button--light">Como funciona</button>
                        </div>
                        <div class="hero__trust">
                            <span class="avatar-stack">
                                @foreach ($featuredOscInitials as $initials)
                                <i>{{ $initials }}</i>
                                @endforeach
                            </span>
                            <span>
                                <strong>{{ $totalOscs }} organizações</strong>
                                conectadas em Camaçari
                            </span>
                        </div>
                    </div>
                    <div class="hero__visual">
                        <img alt="Voluntários trabalhando juntos em uma ação comunitária" src="https://images.unsplash.com/photo-1735504646725-0a5e69c4c9ac?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixlib=rb-4.1.0&q=85&w=1080">
                        <div class="hero__impact-card">
                            <span class="impact-icon"><span x-html="getIcon('heart')"></span></span>
                            <div>
                                <strong>{{ $monthlyInterests }} conexões</strong>
                                <span>registradas neste mês</span>
                            </div>
                        </div>
                        <div class="hero__verified-card">
                            <span x-html="getIcon('shield')"></span>
                            <span>
                                <strong>Rede segura</strong>
                                Dados protegidos
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Quick Stats -->
                <section class="quick-stats">
                    <article><strong>{{ $totalOscs }}</strong><span>OSCs cadastradas</span></article>
                    <article><strong>{{ $activeDemands }}</strong><span>Demandas ativas</span></article>
                    <article><strong>{{ $completedDemands }}</strong><span>Demandas concluídas</span></article>
                    <article><strong>{{ $totalInterests }}</strong><span>Manifestações recebidas</span></article>
                </section>

                <!-- Demands -->
                <section class="demands-section" id="demandas">
                    <div class="section-heading">
                        <div>
                            <span class="eyebrow">Ajude agora</span>
                            <h2>Necessidades da nossa comunidade</h2>
                            <p>Cada demanda foi publicada diretamente por uma organização da rede.</p>
                        </div>
                        <div class="section-counter">
                            <strong x-text="filteredDemands.length"></strong>
                            <span>demandas encontradas</span>
                        </div>
                    </div>

                    <div class="filters">
                        <label class="search-box">
                            <span x-html="getIcon('search')"></span>
                            <input type="search" placeholder="Buscar por causa ou organização" x-model="search">
                        </label>
                        <select x-model="category" aria-label="Filtrar por categoria">
                            <option>Todas</option>
                            <option>Alimentos</option>
                            <option>Voluntariado</option>
                            <option>Materiais</option>
                            <option>Vestuário</option>
                            <option>Higiene</option>
                        </select>
                        <button @click="onlyUrgent = !onlyUrgent" :class="onlyUrgent ? 'urgent-toggle urgent-toggle--active' : 'urgent-toggle'" type="button">
                            <span x-html="getIcon('clock')"></span> Somente urgentes
                        </button>
                    </div>

                    <template x-if="filteredDemands.length > 0">
                        <div class="demand-grid">
                            <template x-for="demand in filteredDemands" :key="demand.id">
                                <article class="demand-card">
                                    <div class="demand-card__top">
                                        <div class="org-avatar" x-text="demand.initials"></div>
                                        <div class="demand-card__badges">
                                            <template x-if="demand.urgent">
                                                <span class="badge badge--urgent">Urgente</span>
                                            </template>
                                            <span class="badge" :class="demand.status === 'Aberta' ? 'badge--success' : 'badge--blue'" x-text="demand.status"></span>
                                        </div>
                                    </div>
                                    <div class="demand-card__body">
                                        <span class="eyebrow" x-text="demand.category"></span>
                                        <h3 x-text="demand.title"></h3>
                                        <p class="demand-card__org" x-text="demand.organization"></p>
                                        <p class="demand-card__description" x-text="demand.description"></p>
                                        <div class="demand-card__meta">
                                            <span>
                                                <span x-html="getIcon('box')"></span>
                                                <span x-text="demand.quantity"></span>
                                            </span>
                                            <span>
                                                <span x-html="getIcon('location')"></span>
                                                <span x-text="demand.neighborhood"></span>
                                            </span>
                                        </div>
                                        <div class="progress-copy">
                                            <span>Progresso da demanda</span>
                                            <strong><span x-text="demand.progress"></span>%</strong>
                                        </div>
                                        <div class="progress">
                                            <span :style="`width: ${demand.progress}%`"></span>
                                        </div>
                                    </div>
                                    <div class="demand-card__footer">
                                        <span>
                                            <span x-html="getIcon('calendar')"></span>
                                            Até <span x-text="demand.deadline"></span>
                                        </span>
                                        <button @click="openDemand(demand)" class="button button--primary">
                                            Quero ajudar <span x-html="getIcon('arrow', 'size-4')"></span>
                                        </button>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </template>

                    <template x-if="filteredDemands.length === 0">
                        <div class="empty-state">
                            <span x-html="getIcon('search')"></span>
                            <h3>Nenhuma demanda encontrada</h3>
                            <p>Tente mudar os filtros para ver outras oportunidades.</p>
                            <button @click="search = ''; category = 'Todas'; onlyUrgent = false" class="button button--secondary">Limpar filtros</button>
                        </div>
                    </template>
                </section>

                <!-- How it works -->
                <section class="how-section" id="como-funciona">
                    <div class="section-heading section-heading--center">
                        <div>
                            <span class="eyebrow">Simples e transparente</span>
                            <h2>Ajudar ficou mais fácil</h2>
                            <p>Em três passos, sua vontade se transforma em impacto real.</p>
                        </div>
                    </div>
                    <div class="steps">
                        <article>
                            <span class="step-number">01</span>
                            <div class="step-icon"><span x-html="getIcon('search')"></span></div>
                            <h3>Encontre uma causa</h3>
                            <p>Use os filtros para descobrir necessidades que combinam com a forma como você quer ajudar.</p>
                        </article>
                        <article>
                            <span class="step-number">02</span>
                            <div class="step-icon"><span x-html="getIcon('heart')"></span></div>
                            <h3>Manifeste seu interesse</h3>
                            <p>Preencha um formulário rápido. Não precisa criar uma conta ou compartilhar dados publicamente.</p>
                        </article>
                        <article>
                            <span class="step-number">03</span>
                            <div class="step-icon"><span x-html="getIcon('people')"></span></div>
                            <h3>Conecte-se com a OSC</h3>
                            <p>A organização recebe seu contato e combina diretamente os próximos passos com você.</p>
                        </article>
                    </div>
                </section>

                <!-- About -->
                <section class="about-section" id="sobre">
                    <div class="about-section__copy">
                        <span class="eyebrow">Uma rede feita em Camaçari</span>
                        <h2>Informação centralizada. Impacto multiplicado.</h2>
                        <p>A Redcam aproxima organizações sociais, apoiadores e a UOSCC em um ambiente confiável. Aqui, necessidades reais não se perdem no fluxo das redes sociais.</p>
                        <ul>
                            <li><span x-html="getIcon('check')"></span> Demandas organizadas e atualizadas</li>
                            <li><span x-html="getIcon('check')"></span> Contatos protegidos pela LGPD</li>
                            <li><span x-html="getIcon('check')"></span> Acompanhamento do status de cada causa</li>
                        </ul>
                    </div>
                    <div class="about-section__quote">
                        <span x-html="getIcon('spark', 'size-7')"></span>
                        <blockquote>“Quando a informação chega à pessoa certa, a solidariedade vira ação.”</blockquote>
                        <p>Rede UOSCC · Camaçari</p>
                    </div>
                </section>

                <!-- CTA -->
                <section class="cta-section">
                    <span class="eyebrow">Faça parte desta rede</span>
                    <h2>Existe uma causa esperando por você.</h2>
                    <p>Doe materiais, compartilhe conhecimento ou ofereça seu tempo. Toda contribuição começa com uma conexão.</p>
                    <button @click="scrollToDemands" class="button button--light">
                        Ver demandas abertas <span x-html="getIcon('arrow')"></span>
                    </button>
                </section>
            </main>

            <footer class="site-footer">
                <div>
                    <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="logo" type="button" aria-label="Ir para o início">
                        <span class="logo__mark" x-html="getIcon('heart', 'size-5')"></span>
                        <span class="logo__copy">
                            <strong>Redcam</strong>
                            <small>Rede que transforma</small>
                        </span>
                    </button>
                    <p>Conectando quem quer ajudar com quem transforma Camaçari todos os dias.</p>
                </div>
                <div>
                    <strong>Navegue</strong>
                    <button @click="scrollToDemands">Demandas</button>
                    <button @click="showLogin = true">Área da OSC</button>
                    <a class="text-sm text-muted no-underline transition-colors hover:text-primary" href="{{ url('/admin') }}">Portal administrativo</a>
                    <button @click="scrollToAbout">Sobre a rede</button>
                </div>
                <div>
                    <strong>Transparência</strong>
                    <button>Privacidade e LGPD</button>
                    <button>Termos de uso</button>
                    <button>Fale conosco</button>
                </div>
                <div class="footer-signature">
                    <strong>UOSCC / Redcam</strong>
                    <span>Um projeto pela comunidade de Camaçari.</span>
                </div>
            </footer>
            <div class="footer-bottom">
                <span>© 2025 Redcam. Todos os direitos reservados.</span>
                <span>Protótipo MVP · Versão A</span>
            </div>
        </div>
    </template>

    <!-- ================= MODALS ================= -->

    <!-- Interest Flow Modal -->
    <template x-if="selectedDemand">
        <div class="modal-backdrop" role="dialog" aria-modal="true" @click.self="selectedDemand = null">
            <button @click="selectedDemand = null" class="modal-close" aria-label="Fechar janela">
                <span x-html="getIcon('close')"></span>
            </button>

            <!-- Step: Detail -->
            <template x-if="flowStep === 'detail'">
                <div class="modal modal--large">
                    <div class="detail-layout">
                        <aside class="detail-aside">
                            <span class="eyebrow">Você está ajudando</span>
                            <div class="org-avatar org-avatar--large" x-text="selectedDemand.initials"></div>
                            <h2 x-text="selectedDemand.organization"></h2>
                            <p>Organização da sociedade civil atuante em Camaçari, conectada à rede UOSCC.</p>
                            <div class="verified">
                                <span x-html="getIcon('shield')"></span>
                                Organização cadastrada
                            </div>
                        </aside>
                        <section class="detail-content">
                            <div class="demand-card__badges">
                                <template x-if="selectedDemand.urgent">
                                    <span class="badge badge--urgent">Urgente</span>
                                </template>
                                <span class="badge badge--success" x-text="selectedDemand.status"></span>
                            </div>
                            <span class="eyebrow" x-text="selectedDemand.category"></span>
                            <h2 x-text="selectedDemand.title"></h2>
                            <p class="detail-description" x-text="selectedDemand.description"></p>
                            <div class="detail-grid">
                                <div>
                                    <span x-html="getIcon('box')"></span>
                                    <span>Quantidade</span>
                                    <strong x-text="selectedDemand.quantity"></strong>
                                </div>
                                <div>
                                    <span x-html="getIcon('calendar')"></span>
                                    <span>Prazo</span>
                                    <strong x-text="selectedDemand.deadline"></strong>
                                </div>
                                <div>
                                    <span x-html="getIcon('location')"></span>
                                    <span>Localização</span>
                                    <strong x-text="selectedDemand.neighborhood"></strong>
                                </div>
                            </div>
                            <div class="privacy-note">
                                <span x-html="getIcon('shield')"></span>
                                Seus dados serão enviados somente para esta organização.
                            </div>
                            <button @click="flowStep = 'form'" class="button button--primary w-full">
                                Manifestar interesse <span x-html="getIcon('arrow')"></span>
                            </button>
                        </section>
                    </div>
                </div>
            </template>

            <!-- Step: Form -->
            <template x-if="flowStep === 'form'">
                <div class="modal modal--large">
                    <form class="interest-form" @submit.prevent="submitInterest">
                        <div>
                            <span class="eyebrow">Falta pouco</span>
                            <h2>Como você quer ajudar?</h2>
                            <p>Não é necessário criar uma conta. A <strong x-text="selectedDemand.organization"></strong> receberá seu contato.</p>
                        </div>
                        <div class="form-grid">
                            <label class="field">
                                <span class="field__label">Seu nome</span>
                                <input name="nome_apoiador" type="text" placeholder="Nome completo" required>
                            </label>
                            <label class="field">
                                <span class="field__label">Tipo de contribuição</span>
                                <select name="tipo_interesse" required>
                                    <option disabled selected value="">Selecione uma opção</option>
                                    <option value="Apoiador / Doação">Apoiador / Doação</option>
                                    <option value="Voluntário">Voluntário</option>
                                    <option value="Aluno">Aluno</option>
                                </select>
                            </label>
                            <label class="field">
                                <span class="field__label">Telefone</span>
                                <input name="telefone" type="tel" placeholder="(71) 99999-9999" @input="formError = ''">
                            </label>
                            <label class="field">
                                <span class="field__label">E-mail</span>
                                <input name="email" type="email" placeholder="voce@email.com" @input="formError = ''">
                            </label>
                        </div>
                        <label class="field">
                            <span class="field__label">Mensagem</span>
                            <textarea name="mensagem" placeholder="Conte brevemente como você pode contribuir..." rows="4"></textarea>
                            <span class="field__hint">Informe ao menos um meio de contato: telefone ou e-mail.</span>
                        </label>
                        <input type="hidden" name="id_demanda" :value="selectedDemand.id">
                        <template x-if="formError">
                            <p class="form-error" role="alert" x-text="formError"></p>
                        </template>
                        <label class="consent">
                            <input type="checkbox" required>
                            <span>Concordo em compartilhar meus dados com esta OSC para retorno sobre esta demanda.</span>
                        </label>
                        <div class="form-actions">
                            <button type="button" @click="flowStep = 'detail'" class="button button--secondary">Voltar</button>
                            <button type="submit" class="button button--primary">Enviar interesse <span x-html="getIcon('arrow')"></span></button>
                        </div>
                    </form>
                </div>
            </template>

            <!-- Step: Success -->
            <template x-if="flowStep === 'success'">
                <div class="modal">
                    <div class="success-state">
                        <div class="success-state__icon">
                            <span x-html="getIcon('check', 'size-8')"></span>
                        </div>
                        <span class="eyebrow">Interesse enviado</span>
                        <h2>Obrigado por fazer parte dessa rede!</h2>
                        <p>Seus dados foram compartilhados com segurança apenas com a <strong x-text="selectedDemand.organization"></strong>. A organização entrará em contato com você.</p>
                        <div class="privacy-note">
                            <span x-html="getIcon('shield')"></span>
                            Seus dados de contato não ficam visíveis publicamente.
                        </div>
                        <button @click="selectedDemand = null" class="button button--primary w-full">Voltar para as demandas</button>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Login Modal -->
    <template x-if="showLogin">
        <div class="modal-backdrop" role="dialog" aria-modal="true" @click.self="showLogin = false">
            <button @click="showLogin = false" class="modal-close" aria-label="Fechar janela">
                <span x-html="getIcon('close')"></span>
            </button>
            <div class="modal">
                <form class="login-form" @submit.prevent="submitLogin($event)">
                    <div class="login-icon">
                        <span x-html="getIcon(loginRole === 'admin' ? 'shield' : 'user')"></span>
                    </div>
                    <span class="eyebrow" x-text="loginRole === 'admin' ? 'Portal administrativo' : 'Área restrita'"></span>
                    <h2 x-text="loginRole === 'admin' ? 'Acesse a gestão da rede' : 'Acesse sua organização'"></h2>
                    <p x-text="loginRole === 'admin' ? 'Gerencie as OSCs cadastradas e acompanhe seus projetos.' : 'Gerencie demandas e acompanhe quem quer ajudar.'"></p>
                    <label class="field">
                        <span class="field__label">E-mail</span>
                        <input name="email" type="email" autocomplete="username" required @input="loginError = ''">
                    </label>
                    <label class="field">
                        <span class="field__label">Senha</span>
                        <input name="password" type="password" autocomplete="current-password" required @input="loginError = ''">
                    </label>
                    <template x-if="loginError">
                        <p class="form-error" role="alert" x-text="loginError"></p>
                    </template>
                    <button type="submit" class="button button--primary w-full">
                        <span x-text="loginRole === 'admin' ? 'Entrar no portal administrativo' : 'Entrar no painel'"></span>
                        <span x-html="getIcon('arrow')"></span>
                    </button>
                    <template x-if="loginRole === 'admin'">
                        <a class="login-setup-link" href="{{ url('/admin') }}">Primeiro acesso? Configurar administrador</a>
                    </template>
                </form>
            </div>
        </div>
    </template>

    <script type="application/json" id="demandas-data">
        @json($demandas)
    </script>
    <script type="application/json" id="authenticated-osc-data">
        @json($authenticatedOsc)
    </script>

    <!-- Alpine.js Logic -->
    <script>
        function appData() {
            return {
                demands: JSON.parse(document.getElementById('demandas-data').textContent),
                authenticatedOsc: JSON.parse(document.getElementById('authenticated-osc-data').textContent),
                search: '',
                category: 'Todas',
                onlyUrgent: false,
                showLogin: false,
                loginRole: 'osc',
                dashboard: Boolean(JSON.parse(document.getElementById('authenticated-osc-data').textContent)),
                loginError: '',
                menuOpen: false,
                selectedDemand: null,
                showCreate: false,
                flowStep: 'detail',
                formError: '',

                icons: {
                    arrow: '<path d="M5 12h14m-6-6 6 6-6 6" />',
                    box: '<path d="m4 7 8-4 8 4-8 4-8-4Z" /><path d="M4 7v10l8 4 8-4V7M12 11v10" />',
                    calendar: '<rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" />',
                    check: '<path d="m5 12 4 4L19 6" />',
                    chevron: '<path d="m9 18 6-6-6-6" />',
                    clock: '<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />',
                    close: '<path d="m6 6 12 12M18 6 6 18" />',
                    heart: '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z" />',
                    home: '<path d="m3 11 9-8 9 8" /><path d="M5 10v10h14V10M9 20v-6h6v6" />',
                    location: '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" />',
                    menu: '<path d="M4 7h16M4 12h16M4 17h16" />',
                    people: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />',
                    plus: '<path d="M12 5v14M5 12h14" />',
                    search: '<circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" />',
                    shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" /><path d="m9 12 2 2 4-4" />',
                    spark: '<path d="m12 3 1.4 4.6L18 9l-4.6 1.4L12 15l-1.4-4.6L6 9l4.6-1.4L12 3ZM19 16l.7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7L19 16Z" />',
                    user: '<circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" />'
                },

                getIcon(name, className = 'size-5') {
                    const paths = this.icons[name] || '';
                    return `<svg aria-hidden="true" class="${className}" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24">${paths}</svg>`;
                },

                get filteredDemands() {
                    return this.demands.filter(demand => {
                        const matchesSearch = demand.title.toLowerCase().includes(this.search.toLowerCase()) ||
                            demand.organization.toLowerCase().includes(this.search.toLowerCase());
                        const matchesCategory = this.category === 'Todas' || demand.category === this.category;
                        return matchesSearch && matchesCategory && (!this.onlyUrgent || demand.urgent);
                    });
                },

                scrollToDemands() {
                    document.getElementById('demandas')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    this.menuOpen = false;
                },

                scrollToHow() {
                    document.getElementById('como-funciona')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                    this.menuOpen = false;
                },

                scrollToAbout() {
                    document.getElementById('sobre')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                    this.menuOpen = false;
                },

                openLogin(role) {
                    this.loginRole = role;
                    this.loginError = '';
                    this.showLogin = true;
                    this.menuOpen = false;
                },

                async submitInterest(event) {
                    const form = event.target;
                    this.formError = '';

                    try {
                        const response = await fetch('/manifestar', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: new FormData(form)
                        });
                        const result = await response.json();

                        if (!response.ok) {
                            this.formError = Object.values(result.errors || {}).flat()[0] || result.message || 'Não foi possível registrar seu interesse.';
                            return;
                        }

                        this.flowStep = 'success';
                    } catch {
                        this.formError = 'Não foi possível enviar sua manifestação. Tente novamente.';
                    }
                },

                async submitLogin(event) {
                    this.loginError = '';
                    const isAdminLogin = this.loginRole === 'admin';

                    try {
                        const response = await fetch(isAdminLogin ? '/admin/login' : '/osc/login', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: new FormData(event.target)
                        });
                        const result = await response.json();

                        if (!response.ok) {
                            this.loginError = Object.values(result.errors || {}).flat()[0] || 'Não foi possível entrar. Verifique suas credenciais.';
                            return;
                        }

                        if (isAdminLogin) {
                            window.location.href = result.redirect;
                            return;
                        }

                        this.authenticatedOsc = result.osc;
                        this.dashboard = true;
                        this.showLogin = false;
                    } catch {
                        this.loginError = 'Não foi possível conectar. Tente novamente.';
                    }
                },

                async submitLogout() {
                    await fetch('/osc/logout', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    this.authenticatedOsc = null;
                    this.dashboard = false;
                },

                openDemand(demand) {
                    this.selectedDemand = demand;
                    this.flowStep = 'detail';
                    this.formError = '';
                }
            }
        }
    </script>
</body>

</html>
