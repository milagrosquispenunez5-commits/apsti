<?php
// VISTA — Dashboard de mensajes recibidos.
// Los datos vienen del modelo; el botón Eliminar envía al controlador.
require_once __DIR__ . '/../modelo/mensaje.php';
$mensajes = listarMensajes();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mensajes recibidos | ASPTI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-brand: #b30000;
            --color-brand-deep: #7a0000;
            --color-ink: #231e1c;
            --color-body: #4a4340;
            --color-bg: #f7f5f3;
            --color-surface: #ffffff;
            --color-border: #e9e2dd;
            --radius-sm: 8px;
            --radius-md: 16px;
            --shadow-sm: 0 2px 10px rgba(70, 15, 10, .08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--color-bg);
            color: var(--color-body);
            min-height: 100vh;
        }

        header {
            background: var(--color-brand);
            color: #fff;
            padding: 24px 20px;
        }

        .contenedor { max-width: 1100px; margin: 0 auto; }

        header .contenedor {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        header h1 { font-size: 1.4rem; font-weight: 600; }

        header a {
            color: #fff;
            text-decoration: none;
            font-size: .9rem;
            border: 1px solid rgba(255, 255, 255, .5);
            padding: 8px 16px;
            border-radius: 999px;
        }

        header a:hover { background: var(--color-brand-deep); }

        main { padding: 28px 20px 60px; }

        .resumen {
            font-size: .95rem;
            margin-bottom: 20px;
        }

        .resumen strong { color: var(--color-brand); font-size: 1.1rem; }

        .paneles {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .panel {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 20px;
        }

        .panel h2 {
            font-size: 1.05rem;
            color: var(--color-brand-deep);
            margin-bottom: 10px;
        }

        .panel p {
            font-size: .95rem;
            line-height: 1.6;
            color: var(--color-body);
        }

        .panel ul {
            list-style: none;
            display: grid;
            gap: 8px;
            margin-top: 10px;
        }

        .panel li {
            background: var(--color-bg);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: .9rem;
            color: var(--color-ink);
        }

        .mensaje {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 20px 22px;
            margin-bottom: 16px;
        }

        .mensaje-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .mensaje-cabecera h2 {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--color-ink);
        }

        .mensaje-cabecera a {
            color: var(--color-brand);
            text-decoration: none;
            font-size: .9rem;
        }

        .mensaje-cabecera a:hover { text-decoration: underline; }

        .fecha { font-size: .8rem; color: #948a85; white-space: nowrap; }

        .mensaje p { font-size: .95rem; line-height: 1.6; }

        .mensaje form { margin-top: 14px; text-align: right; }

        .mensaje button {
            font-family: inherit;
            font-size: .8rem;
            color: var(--color-brand);
            background: none;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: 6px 14px;
            cursor: pointer;
        }

        .mensaje button:hover {
            background: var(--color-brand);
            border-color: var(--color-brand);
            color: #fff;
        }

        .vacio {
            background: var(--color-surface);
            border: 1px dashed var(--color-border);
            border-radius: var(--radius-md);
            padding: 60px 20px;
            text-align: center;
            font-size: .95rem;
        }
    </style>
</head>

<body>
    <header>
        <div class="contenedor">
            <h1>📬 Mensajes recibidos</h1>
            <a href="../ASPTI.html">← Volver al sitio</a>
        </div>
    </header>

    <main class="contenedor">

        <p class="resumen">
            Tienes <strong><?php echo count($mensajes); ?></strong>
            mensaje<?php echo count($mensajes) === 1 ? '' : 's'; ?> de contacto.
        </p>

        <section class="paneles">
            <article class="panel">
                <h2>🗂️ Mesa de partes</h2>
                <p>Centraliza los trámites, solicitudes y seguimiento del área administrativa.</p>
                <ul>
                    <li>Solicitudes registradas</li>
                    <li>Trámites en revisión</li>
                    <li>Atención prioritaria</li>
                </ul>
            </article>

            <article class="panel">
                <h2>📚 Biblioteca</h2>
                <p>Accede a recursos, guías y materiales de apoyo para docentes y estudiantes.</p>
                <ul>
                    <li>Guías de estudio</li>
                    <li>Material digital</li>
                    <li>Recursos recomendados</li>
                </ul>
            </article>

            <article class="panel">
                <h2>💬 Mensajes</h2>
                <p>Revisa los mensajes recibidos desde la página principal del sitio.</p>
                <ul>
                    <li>Mensajes nuevos</li>
                    <li>Respuestas pendientes</li>
                    <li>Historial de contacto</li>
                </ul>

                <div style="margin-top: 14px;">
                    <?php if (count($mensajes) === 0): ?>
                        <div class="vacio">Aún no has recibido mensajes. Cuando alguien use el formulario de contacto, aparecerá aquí.</div>
                    <?php endif; ?>

                    <?php foreach ($mensajes as $fila): ?>
                        <article class="mensaje">
                            <div class="mensaje-cabecera">
                                <h2><?php echo htmlspecialchars($fila['nombre']); ?></h2>
                                <a href="mailto:<?php echo htmlspecialchars($fila['correo']); ?>">
                                    <?php echo htmlspecialchars($fila['correo']); ?>
                                </a>
                                <span class="fecha"><?php echo date('d/m/Y H:i', strtotime($fila['fecha_envio'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($fila['mensaje'])); ?></p>
                            <form method="post" action="../controlador/eliminar.php" onsubmit="return confirm('¿Eliminar este mensaje?')">
                                <button type="submit" name="eliminar" value="<?php echo $fila['id']; ?>">Eliminar</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            </article>
        </section>

    </main>
</body>

</html>
