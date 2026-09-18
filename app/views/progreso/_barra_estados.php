<?php
defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Barra 100 % apilada de los tres estados de progreso. Complementa
 * (no reemplaza) las tarjetas con los números.
 *
 * Espera en el ámbito que la incluye:
 *   $conteo        array  ["Logrado"=>int, "En proceso"=>int, "Requiere apoyo"=>int]
 *   $barraTitulo   string (opcional) leyenda de la figura
 *
 * Los colores son la paleta de estado del proyecto (verde / ocre /
 * granate), la misma de las insignias y los emojis ✅ 🟡 🆘 — el color
 * nunca va solo: cada segmento y cada entrada de la leyenda llevan su
 * etiqueta.
 */

$__seg = [
    ["Logrado",        "✅", "var(--exito)",   (int) ($conteo["Logrado"] ?? 0)],
    ["En proceso",     "🟡", "var(--ocre)",    (int) ($conteo["En proceso"] ?? 0)],
    ["Requiere apoyo", "🆘", "var(--peligro)", (int) ($conteo["Requiere apoyo"] ?? 0)],
];
$__total = $__seg[0][3] + $__seg[1][3] + $__seg[2][3];

if ($__total > 0):
    $__resumen = [];
    foreach ($__seg as [$__n, , , $__v]) {
        $__resumen[] = "$__n $__v";
    }
?>
<figure class="barra-estados">
    <?php if (!empty($barraTitulo)): ?>
        <figcaption class="barra-estados__titulo"><?= htmlspecialchars($barraTitulo) ?></figcaption>
    <?php endif; ?>

    <div class="barra-estados__track" role="img"
         aria-label="<?= htmlspecialchars(implode(", ", $__resumen) . " (de $__total en total)") ?>">
        <?php foreach ($__seg as [$nombre, $emoji, $color, $valor]): ?>
            <?php if ($valor <= 0) continue; ?>
            <?php $pct = round($valor / $__total * 100, 1); ?>
            <span class="barra-estados__seg" data-estado="<?= htmlspecialchars($nombre) ?>"
                  style="width: <?= $pct ?>%; background: <?= $color ?>;"
                  title="<?= htmlspecialchars("$nombre: $valor · " . round($pct) . "%") ?>">
                <?php if ($pct >= 12): ?><span class="barra-estados__seg-num"><?= $valor ?></span><?php endif; ?>
            </span>
        <?php endforeach; ?>
    </div>

    <ul class="barra-estados__leyenda">
        <?php foreach ($__seg as [$nombre, $emoji, $color, $valor]): ?>
            <li>
                <span class="barra-estados__punto" data-estado="<?= htmlspecialchars($nombre) ?>" style="background: <?= $color ?>;" aria-hidden="true"></span>
                <?= $emoji ?> <?= htmlspecialchars($nombre) ?> — <strong><?= $valor ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>
</figure>
<?php endif; ?>
