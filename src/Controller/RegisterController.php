<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Service\SireneSiretValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SireneSiretValidator $siretValidator,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $siretValidation = $siretValidator->validate((string) $user->getSiret());

            if (!$siretValidation['valid']) {
                $form->get('siret')->addError(new FormError($siretValidation['message']));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $user
                ->setPassword($passwordHasher->hashPassword($user, $plainPassword))
                ->setRoles(['ROLE_USER'])
            ;

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/register/check-email', name: 'app_register_check_email', methods: ['GET'])]
    public function checkEmail(Request $request, UserRepository $userRepository): JsonResponse
    {
        $email = trim((string) $request->query->get('email', ''));
        $isFormatValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

        return $this->json([
            'valid' => $isFormatValid,
            'available' => $isFormatValid && $userRepository->findOneBy(['email' => $email]) === null,
        ]);
    }

    #[Route(path: '/register/check-siret', name: 'app_register_check_siret', methods: ['GET'])]
    public function checkSiret(
        Request $request,
        UserRepository $userRepository,
        SireneSiretValidator $siretValidator,
    ): JsonResponse
    {
        $siret = trim((string) $request->query->get('siret', ''));
        $validation = $siretValidator->validate($siret);
        $available = $validation['valid'] && $userRepository->findOneBy(['siret' => $siret]) === null;

        if ($validation['valid'] && !$available) {
            $validation['message'] = 'Ce numéro de SIRET est déjà utilisé.';
        }

        return $this->json([
            'valid' => $validation['valid'],
            'available' => $available,
            'message' => $validation['message'],
        ]);
    }
}
