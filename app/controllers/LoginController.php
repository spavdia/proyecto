<?php

declare(strict_types=1);

namespace Sergio\App\controllers;

use Sergio\App\models\LoginModel;
use Sergio\Lib\SessionManager;

class LoginController extends Controller
{
    public static function mostrarLoginForm(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioAutenticado('usuario', 'panel');

        $flash = SessionManager::getMensajeFlash();
        $emailAnterior = SessionManager::get('email_anterior');

        self::view('home/login_view', [
            'tituloPagina'  => 'PipelineDesk | Iniciar sesión',
            'mensajeFlash'  => $flash['mensaje'] ?? null,
            'iconoFlash'    => $flash['icono'] ?? null,
            'claseFlash'    => $flash['clase'] ?? 'error',
            'errores'      => [],
            'email'        => ''
        ]);
    }

    public static function login(): void
    {
        SessionManager::iniciarSesion();

        $errores = [];
        $email = trim($_POST['email'] ?? '');
        $pass  = trim($_POST['password'] ?? '');

        if ($email === '') {
            $errores['email'] = 'Error. Debes rellenar email.';
        }

        if ($pass === '') {
            $errores['password'] = 'Error. Debes rellenar contraseña.';
        }

        if (!empty($errores)) {
            self::view('home/login_view', [
                'tituloPagina' => 'PipelineDesk | Iniciar sesión',
                'errores'      => $errores,
                'email'        => $email,
                'mensajeFlash' => null,
                'iconoFlash'   => null,
                'claseFlash'   => 'error'
            ]);
            return;
        }
        $lm = new LoginModel();
        $usuario = $lm->findByEmail($email);

        if (!$usuario) {
            SessionManager::setMensajeFlash(
                'Credenciales incorrectas.',
                '⚠',
                'error'
            );
            SessionManager::set('email_anterior', $email);
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        if ((int) $usuario['activo'] !== 1) {
            SessionManager::setMensajeFlash(
                'Tu usuario está inactivo.',
                '⛔',
                'error'
            );
            SessionManager::set('email_anterior', $email);
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        if (!password_verify($pass, $usuario['password_hash'])) {
            SessionManager::setMensajeFlash(
                'Credenciales incorrectas.',
                '⚠',
                'error'
            );

            SessionManager::set('email_anterior', $email);
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        //si todo Ok => guardamos usuario en sesión sin password_hash
        unset($usuario['password_hash']);
        SessionManager::set('usuario', $usuario);
        SessionManager::setMensajeFlash(
            'Bienvenido a PipelineDesk, ' . $usuario['nombre'] . '.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }

    public static function logout(): void
    {
        SessionManager::destruirSesion();
        SessionManager::iniciarSesion();
        SessionManager::setMensajeFlash(
            'Has cerrado sesión correctamente.',
            'ℹ',
            'info'
        );

        header('Location: ' . BASE_URL . 'login');
        exit();
    }
}
