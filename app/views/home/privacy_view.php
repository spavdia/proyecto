<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Política de privacidad';
$archivoCssVista = '';
$archivoJsVista = '';

require_once APP_ROOT . '/app/views/layouts/header.php';
?>

<main class="privacidad-page">
    <div class="privacidad-contenedor">
        <section class="privacidad-hero">
            <p class="privacidad-etiqueta">Información legal</p>
            <h1>Política de privacidad</h1>
            <p class="privacidad-intro">
                En Pipeline CRM respetamos la privacidad de nuestros usuarios, clientes potenciales y contactos comerciales.
                Esta política explica de forma clara cómo tratamos la información personal dentro de la aplicación y en los formularios asociados.
            </p>
        </section>

        <div class="privacidad-grid">
            <section class="privacidad-bloque">
                <h2>1. Responsable del tratamiento</h2>
                <p>
                    El responsable del tratamiento de los datos es <strong>Pipeline CRM</strong>, plataforma orientada a la gestión comercial,
                    seguimiento de leads, tareas y actividad interna del equipo de ventas.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>2. Qué datos recopilamos</h2>
                <p>
                    Podemos tratar datos como nombre, email, teléfono, servicio de interés, notas comerciales, estado del lead y actividad registrada dentro del CRM.
                </p>
                <p>
                    También se pueden almacenar datos internos relacionados con usuarios de la aplicación, tareas asignadas, historial de cambios y productividad comercial.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>3. Finalidad del tratamiento</h2>
                <p>
                    Los datos se utilizan para gestionar oportunidades comerciales, organizar seguimientos, registrar interacciones con leads y mejorar la coordinación del equipo.
                </p>
                <p>
                    La información también puede emplearse para responder solicitudes realizadas desde formularios públicos y para el control interno de la actividad comercial.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>4. Base jurídica</h2>
                <p>
                    El tratamiento de datos se basa en el consentimiento del interesado cuando contacta con la empresa,
                    en la ejecución de una relación precontractual o contractual, y en el interés legítimo para la gestión comercial y organizativa del negocio.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>5. Conservación de los datos</h2>
                <p>
                    Los datos se conservarán durante el tiempo necesario para cumplir la finalidad para la que fueron recopilados
                    y, posteriormente, durante los plazos legalmente exigibles si existiera obligación normativa.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>6. Cesión de datos</h2>
                <p>
                    Pipeline CRM no cede datos personales a terceros salvo obligación legal o cuando sea estrictamente necesario
                    para la prestación de servicios vinculados al funcionamiento de la plataforma.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>7. Derechos de los usuarios</h2>
                <p>
                    Los usuarios pueden ejercer sus derechos de acceso, rectificación, supresión, limitación, oposición y portabilidad cuando proceda.
                </p>
                <p>
                    Para ello, podrán solicitarlo mediante los canales de contacto de la empresa, identificándose adecuadamente y detallando su solicitud.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>8. Seguridad</h2>
                <p>
                    Se aplican medidas razonables de seguridad técnica y organizativa para proteger los datos personales
                    frente a accesos no autorizados, alteración, pérdida o tratamiento indebido.
                </p>
            </section>

            <section class="privacidad-bloque">
                <h2>9. Cambios en esta política</h2>
                <p>
                    Esta política podrá actualizarse cuando existan cambios legales, técnicos o funcionales dentro de la plataforma.
                    La versión publicada en esta página será la vigente en cada momento.
                </p>
            </section>
        </div>

        <div class="privacidad-acciones">
            <a href="<?= BASE_URL ?>" class="boton boton-principal">Volver al inicio</a>
            <a href="<?= BASE_URL . 'contacto' ?>" class="boton boton-volver">Contacto</a>
        </div>
    </div>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>