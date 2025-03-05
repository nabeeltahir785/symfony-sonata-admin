<?php

namespace App\Controller\Impersonate;

use App\Entity\ImpersonationLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/admin/impersonate")
 */
class ImpersonateController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/user/{id}", name="admin_impersonate_user")
     */
    public function impersonateUser(Request $request, User $user)
    {
        // Check if user has impersonation rights
        if (!$this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            throw new AccessDeniedException('You do not have permission to impersonate users.');
        }

        // Don't allow impersonating yourself
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'You cannot impersonate yourself.');
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        // Log the impersonation
        $this->logger->info(sprintf(
            'User "%s" is impersonating user "%s"',
            $this->getUser()->getEmail(),
            $user->getEmail()
        ));

        // Create impersonation log
        $log = new ImpersonationLog();
        $log->setImpersonator($this->getUser());
        $log->setImpersonated($user);
        $log->setAction('enter');
        $log->setIpAddress($request->getClientIp());
        $log->setUserAgent($request->headers->get('User-Agent'));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        // Redirect to dashboard with switch_user parameter
        $url = $this->generateUrl('sonata_admin_dashboard', [
            '_switch_user' => $user->getEmail()
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/exit", name="admin_impersonate_exit")
     */
    public function exitImpersonation(Request $request)
    {
        if (!$this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $this->addFlash('error', 'You are not currently impersonating anyone.');
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        // Log the exit
        $originalUser = $this->get('security.token_storage')->getToken()->getOriginalToken()->getUser();
        $impersonatedUser = $this->getUser();

        $this->logger->info(sprintf(
            'User "%s" stopped impersonating user "%s"',
            $originalUser->getEmail(),
            $impersonatedUser->getEmail()
        ));

        // Create impersonation log
        $log = new ImpersonationLog();
        $log->setImpersonator($originalUser);
        $log->setImpersonated($impersonatedUser);
        $log->setAction('exit');
        $log->setIpAddress($request->getClientIp());
        $log->setUserAgent($request->headers->get('User-Agent'));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        // Redirect to dashboard with exit parameter
        $url = $this->generateUrl('sonata_admin_dashboard', [
            '_switch_user' => '_exit'
        ]);

        return new RedirectResponse($url);
    }
}