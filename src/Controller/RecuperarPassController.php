<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Usuario;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class RecuperarPassController extends AbstractController
{
    #[Route('/recuperarpass', name: 'ctrl_recuperarpass')]
    public function recuperarcontraseña()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        return $this->render('recuperarpass.html.twig');
    }

    #[Route('/procesarecuperar', name: 'ctrl_procesarecuperar')]
    public function procesarecuperar(MailerInterface $mailer, Request $request, EntityManagerInterface $em)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        $email = $request->request->get('email');

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (!$usuario) {
            return $this->render('recuperarpass.html.twig', [
                'error' => 'No se encontró ningún usuario con ese correo electrónico.',
            ]);
        }

        $nuevoToken = bin2hex(random_bytes(16));
        $usuario->setToken($nuevoToken);

        $nuevaFechaExpiracion = (new DateTime())->modify('+1 hour');
        $usuario->setTokenExpiracion($nuevaFechaExpiracion);

        $em->flush();

        $nombreUsuario = $usuario->getNombreUsuario();

        $message = new Email();
        $message->from(new Address('befly@gmail.com', "BeFly"));
        $message->to(new Address($email));
        $message->subject("Verificación de recuperación de contraseña en BeFly");
        $message->html("<h1>Hola, $nombreUsuario!</h1>"
            . "<p>Por favor, haz clic en el siguiente enlace para cambiar la contraseña:</p>"
            . "<a href='http://localhost:8000/verificar_cambiarcontraseña/$email/$nuevoToken'>Verificar cambiar contraseña</a>");
        $mailer->send($message);

        $this->addFlash('mensaje', 'Se ha enviado un correo de verificación a tu email.');
        return $this->redirectToRoute('ctrl_login');
    }

    #[Route('/verificar_cambiarcontraseña/{email}/{token}', name: 'ctrl_verificar_cambiarcontraseña')]
    public function verificar_cambiarcontraseña($email, $token, EntityManagerInterface $em)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (!$usuario) {
            return $this->render('recuperarpass.html.twig', [
                'error' => 'No se encontró ningún usuario con ese correo electrónico.',
            ]);
        }

        if ($usuario->getToken() !== $token) {
            return $this->render('recuperarpass.html.twig', [
                'error' => 'Token inválido.',
            ]);
        }

        if ($usuario->getTokenExpiracion() < new DateTime()) {
            return $this->render('recuperarpass.html.twig', [
                'error' => 'El token ha expirado.',
            ]);
        }

        return $this->render('cambiarcontraseña.html.twig', [
            'email' => $email,
        ]);
    }

    #[Route('/cambiarcontraseña', name: 'ctrl_cambiarcontraseña')]
    public function cambiarcontraseña(Request $request, EntityManagerInterface $em)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ctrl_profile');
        }

        $email = $request->request->get('email');
        $clave = $request->request->get('clave');
        $repetirClave = $request->request->get('repetir_clave');

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (!$usuario) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'No se encontró ningún usuario con ese correo electrónico.',
                'email' => $email,
            ]);
        }

        if (empty($email)) {
            // lo envio aquí, porque si el email está vacío significa que el usuario ha intentado
            // acceder a esta ruta desde la url, ok?
            return $this->redirectToRoute('ctrl_login');
        }

        if (empty($clave) || empty($repetirClave)) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'Complete todos los campos.',
                'email' => $email,
            ]);
        }

        //la contraseña tiene al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.
        $formatoClave = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/";
        if (!preg_match($formatoClave, $clave)) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.',
                'email' => $email,
            ]);
        }

        if ($clave !== $repetirClave) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'Las contraseñas no coinciden.',
                'email' => $email,
            ]);
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $usuario->setClave($claveHash);
        $em->flush();

        $this->addFlash('mensaje', 'Contraseña cambiada');
        return $this->redirectToRoute('ctrl_login');
    }
}