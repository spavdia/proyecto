<?php

declare(strict_types=1);

namespace Sergio\App\controllers;

use Sergio\App\models\LoginModel;
use Sergio\Lib\SessionManager;

class LoginController extends Controller
{
    public static function mostrarLoginForm(): void
    {
        SessionManager::usuarioAutenticado('usuario','panel');
        SessionManager::iniciarSesion();

        $flash = SessionManager::getMensajeFlash();

        self::view('home/login_view', [
            'titulo' => 'Iniciar sesión',
            'flash' => $flash,
        ]);
    }

    public static function login(): void
    {
        SessionManager::iniciarSesion();

        $email = trim($_POST['email'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        if ($email === '' || $pass === '') {
            SessionManager::setMensajeFlash(
                'Debes rellenar correo y contraseña.',
                'warning',
                'error'
            );
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        $loginModel = new LoginModel();
        $usuario = $loginModel->findByEmail($email);

        if (!$usuario) {
            SessionManager::setMensajeFlash(
                'Usuario no existe en base de datos o correo incorrecto.',
                'error',
                'error'
            );
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        if ((int) $usuario['activo'] !== 1) {
            SessionManager::setMensajeFlash(
                'Tu usuario está inactivo.',
                'warning',
                'error'
            );
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        if (!password_verify($pass, $usuario['password_hash'])) {
            SessionManager::setMensajeFlash(
                'Credenciales incorrectas.',
                'error',
                'error'
            );
            header('Location: ' . BASE_URL . 'login');
            exit();
        }

        //si todo Ok => guardamos usuario en sesión sin password_hash
        unset($usuario['password_hash']);
        SessionManager::set('usuario', $usuario);
        SessionManager::setMensajeFlash(
            'Bienvenido, ' . $usuario['nombre'] . '.',
            'success',
            'success'
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
            'info',
            'success'
        );

        header('Location: ' . BASE_URL . 'login');
        exit();
    }
}
