<?php
// Le profil que montre le candidat pour l'employeur
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Employer;
use App\Repository\ConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CandidateProfileController extends AbstractController
{
    #[Route('/profil-candidat/{id}/{conversationId}', name: 'app_candidate_profile')]
    public function show(Candidate $candidate, ?int $conversationId, ConversationRepository $convRepo): Response
    {
        $user = $this->getUser();

        // 1. Utilisateur connecté ?
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        // 2. Si c'est le candidat lui-même, on laisse passer
        if($user === $candidate){
            return $this->render('candidate_profile/index.html.twig', [
                'candidate' => $candidate,
            ]);
        }

        // 3. Si c'est un Employeur, on vérifie s'il y a une conversation avec ce candidat
        if($user instanceof Employer) {
            $conversation = null;

            if($conversationId) {
                // On cherche la conversation précise passée dans l'URL
                $conversation = $convRepo->find($conversationId);

                // Sécurité : on vérifie que l'employeur est bien l'un des participants
                if(!$conversation || $conversation->getEmployer() !== $user) {
                    $this->addFlash('error', "Cette conversation n'existe pas ou ne vous appartient pas.");
                    return $this->redirectToRoute('app_home');
                }
            }else{
                // Si pas d'ID spécifié, on cherche n'importe quelle conversation entre eux
                $conversation = $convRepo->findOneBy([
                    'employer' => $user,
                    'candidate' => $candidate
                ]);
            }

            if($conversation) {
                return $this->render('candidate_profile/index.html.twig', [
                'candidate' => $candidate,
                'conversation' => $conversation,
                ]);
            }
        }

        // 4. Dans tous les autres cas (pas de conv, ou autre rôle), on bloque
        $this->addFlash('error', "Vous ne pouvez consulter que les profils des candidats qui ont postulé à vos missions.");
        return $this->redirectToRoute('app_home');
    }
}
