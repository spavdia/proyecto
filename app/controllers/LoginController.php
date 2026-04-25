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
        SessionManager::usuarioAutenticado('usuario','panel');

        $flash = SessionManager::getMensajeFlash();
        $emailAnterior = SessionManager::get('email_anterior');

        self::view('home/login_view', [
            'tituloPagina'  => 'PipelineDesk | Iniciar sesión',
            'mensajeFlash'  => $flash['mensaje'] ?? null,
            'iconoFlash'    => $flash['icono'] ?? null,
            'claseFlash'    => $flash['clase'] ?? 'error',
            'emailAnterior' => $emailAnterior ?? ''
        ]);
    }

    public static function login(): void
    {
        SessionManager::iniciarSesion();

        $email = trim($_POST['email'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        if ($email === '' || $pass === '') {
             SessionManager::setMensajeFlash(
                'Debes rellenar el correo y la contraseña.',
                '⚠',
                'error'
            );
            SessionManager::set('email_anterior', $email);
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        $loginModel = new LoginModel();
        $usuario = $loginModel->findByEmail($email);

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
