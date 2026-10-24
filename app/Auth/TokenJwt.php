<?php

namespace App\Auth;

use Carbon\CarbonInterface;

/**
 * Token recém-emitido: o JWT assinado, seus claims e quando expira.
 */
final class TokenJwt
{
    public function __construct(
        public readonly string $token,
        public readonly object $claims,
        public readonly CarbonInterface $expiraEm,
    ) {}
}
