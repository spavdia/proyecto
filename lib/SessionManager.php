<?php

namespace Sergio\Lib;

class SessionManager
{
    public static function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function destruirSesion()
    {
        self::iniciarSesion();

        if (session_status() !== PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
        }
    }

    public static function set($clave, $valor)
    {
        self::iniciarSesion();
        $_SESSION[$clave] = $valor;
    }

    public static function get($clave)
    {
        self::iniciarSesion();
        return $_SESSION[$clave] ?? null;
    }

    public static function eliminar($clave)
    {
        self::iniciarSesion();

        if (isset($_SESSION[$clave])) {
            unset($_SESSION[$clave]);
        }
    }

    public static function existe($clave)
    {
        self::iniciarSesion();
        return isset($_SESSION[$clave]);
    }

    public static function usuarioAutenticado($userKey, $redirectTo)
    {
        if (self::get($userKey)) {
            header('Location: ' . $redirectTo);
            exit();
        }
    }

    public static function usuarioNoAutenticado($userKey, $redirectTo)
    {
        if (!self::get($userKey)) {
            header('Location: ' . BASE_URL . $redirectTo);
            exit();
        }
    }

    public static function setMensajeFlash($mensaje, $icono = null, $clase = 'error')
    {
        self::iniciarSesion();

        if (!isset($_SESSION['mensajes_flash']) || !is_array($_SESSION['mensajes_flash'])) {
            $_SESSION['mensajes_flash'] = [];
        }

        $_SESSION['mensajes_flash'][] = [
            'mensaje' => $mensaje,
            'icono'   => $icono,
            'clase'   => $clase
        ];
    }

    public static function getMensajesFlash(bool $limpiar = true): array
    {
        self::iniciarSesion();

        $mensajes = [];

        if (isset($_SESSION['mensajes_flash']) && is_array($_SESSION['mensajes_flash'])) {
            $mensajes = $_SESSION['mensajes_flash'];
        } elseif (isset($_SESSION['mensaje_flash']) && is_array($_SESSION['mensaje_flash'])) {
            $mensajes[] = $_SESSION['mensaje_flash'];
        }

        if ($limpiar) {
            unset($_SESSION['mensajes_flash'], $_SESSION['mensaje_flash']);
        }

        return $mensajes;
    }

    public static function getMensajeFlash()
    {
        self::iniciarSesion();

        $mensajes = self::getMensajesFlash(false);

        if (!empty($mensajes)) {
            return $mensajes[0];
        }

        return null;
    }
}