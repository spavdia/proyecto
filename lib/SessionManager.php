<?php

/**
 * P.Lluyot-2025
 */

namespace Sergio\Lib;

/**
 * Clase SessionManager para gestionar las sesiones de usuario de forma centralizada.
 * Combina lo mejor de los proyectos anteriores.
 */
class SessionManager
{
    /**
     * Inicia la sesión PHP si aún no está iniciada.
     */
    public static function iniciarSesion()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Destruye completamente la sesión actual.
     */
    public static function destruirSesion()
    {
        self::iniciarSesion();
        if (session_status() != PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Establece un valor en una variable de sesión.
     */
    public static function set($clave, $valor)
    {
        self::iniciarSesion();
        $_SESSION[$clave] = $valor;
    }

    /**
     * Obtiene el valor de una variable de sesión.
     */
    public static function get($clave)
    {
        self::iniciarSesion();
        return isset($_SESSION[$clave]) ? $_SESSION[$clave] : null;
    }

    /**
     * Elimina una variable específica de la sesión.
     */
    public static function eliminar($clave)
    {
        self::iniciarSesion();
        if (isset($_SESSION[$clave])) {
            unset($_SESSION[$clave]);
        }
    }

    /**
     * Comprueba si una variable de sesión específica existe.
     */
    public static function existe($clave)
    {
        self::iniciarSesion();
        return isset($_SESSION[$clave]);
    }

    /**
     * Si el usuario está autenticado, redirige a la página indicada.
     * Útil para la página de login (si ya estás logueado, ve al dashboard).
     */
    public static function usuarioAutenticado($userKey, $redirectTo)
    {
        if (self::get($userKey)) {
            header("Location: ". $redirectTo);
            exit();
        }
    }

    /**
     * Si el usuario NO está autenticado, redirige a la página indicada.
     * Útil para proteger rutas privadas.
     */
    public static function usuarioNoAutenticado($userKey, $redirectTo)
    {
        if (!self::get($userKey)) {
            header("Location: " . BASE_URL.$redirectTo);
            exit();
        }
    }

    public static function setMensajeFlash($mensaje, $icono = null, $clase = 'error')
    {
        self::iniciarSesion();
        $_SESSION['mensaje_flash'] = [

            'mensaje' => $mensaje,
            'icono'   => $icono,
            'clase'    => $clase
        ];
    }

    /**
     * Recupera el paquete flash completo y lo elimina de la sesión.
     * * @return array|null Retorna un array asociativo o null si no existe
     */
    public static function getMensajeFlash()
    {
        self::iniciarSesion();
        if (isset($_SESSION['mensaje_flash'])) {
            $flash = $_SESSION['mensaje_flash'];
            unset($_SESSION['mensaje_flash']); // Se elimina tras la primera lectura
            return $flash;
        }
        return null;
    }
}
