<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Conversation;
use App\Entity\Employer;
use App\Entity\Message;
use App\Entity\Mission;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChatController extends AbstractController
{
    /**
     * Fonction qui permet à un candidat de voir la liste de ses discussions
     */
    #[Route('/mes-discussions', name: 'app_candidate_conversations')]
    public function myConversations(ConversationRepository $convRepo)
    {
        $user = $this->getUser();

        //Sécurité : on vérifie que le user est bien candidat
        if(!$user instanceof Candidate)
        {
            return $this->redirectToRoute('app_login');
        }

        // On récupère toutes les conversations où l'utilisateur est le candidat
        $conversations = $convRepo->findBy(['candidate' => $user]);

        return $this->render('chat/candidate_list.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Fonction qui permet à un employeur de voir la liste de ses discussions
     */
    #[Route('/mes-discussions-employeur', name: 'app_employer_conversations')]
    public function employerConversation(ConversationRepository $convRepo)
    {
        $user = $this->getUser();

        //Sécurité : on vérifie que le user est bien un employeur
        if(!$user instanceof Employer)
        {
            return $this->redirectToRoute('app_login');
        }

        // On récupère toutes les conversations où l'utilisateur est l'employeur
        $conversations = $convRepo->findBy(['employer' => $user]);

        return $this->render('chat/employer_list.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Fonction qui permet de postuler et de créer une nouvelle conversation
     */
    #[Route('/postuler/{id}', name: 'app_mission_postuler')]
    public function postuler(Mission $mission, ConversationRepository $convRepo, EntityManagerInterface $em): Response
    {
        //1. On récupère le candidat connecté
        $user = $this->getUser();

        //Sécurité : on vérifie que le user est bien candidat
        if(!$user instanceof Candidate)
        {
            $this->addFlash('error', 'Seuls les candidats peuvent postuler.');
            return $this->redirectToRoute('app_login');
        }

        $employer = $mission->getEmployer();
        //2. On vérifie si une conversation existe déjà entre le candidat et la mission choisie
        $conversation = $convRepo->findOneBy([
            'mission' => $mission,
            'candidate' => $user
        ]);

        //3. Si elle n'existe pas, on la crée
        if(!$conversation){
            $conversation = new Conversation();
            $conversation->setMission($mission);
            $conversation->setCandidate($user);
            $conversation->setEmployer($employer);

            $em->persist($conversation);

            //Création du 1er message automatique
            $welcomeMessage = new Message();
            $welcomeMessage->setContent("Bonjour, je souhaite postuler pour votre mission : " . $mission->getTitle());
            $welcomeMessage->setAuthor($user);
            $welcomeMessage->setConversation($conversation);
            $welcomeMessage->setCreatedAt(new \DateTimeImmutable());

            $em->persist($welcomeMessage);
            $em->flush();

            $this->addFlash('succes', 'Votre candidature a été envoyée !');
        }

        // 4. On redirige vers la vue de cette conversation
        return $this->redirectToRoute('app_chat_view', [
            'id' =>$conversation->getId()
        ]);
    }

    /**
     * Fonction pour voir une discussion précise
     */
    #[Route('/chat/{id}', name: 'app_chat_view')]
    public function viewChat(Conversation $conversation, Request $request, EntityManagerInterface $em)
    {
        //Sécurité : Le user fait il partie de la conversation?
        if($this->getUser() !== $conversation->getCandidate() && $this->getUser() !== $conversation->getEmployer())
            {
                throw $this->createAccessDeniedException("Vous n'avez pas accès à cette conversation.");
            }

            $message = new Message();
            $form= $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $message->setAuthor($this->getUser());
                $message->setConversation($conversation);
                $message->setCreatedAt(new \DateTimeImmutable());

                // Logique pour le destinataire : si l'auteur est le candidat, le destinataire est l'employeur (et vice versa)
                $recipent = ($this->getUser() ===$conversation->getCandidate())
                    ? $conversation->getEmployer()
                    : $conversation->getCandidate();
                $message->setRecipent($recipent);

                $em->persist($message);
                $em->flush();

                return $this->redirectToRoute('app_chat_view', ['id' => $conversation->getId()]);
            }

            return $this->render('chat/view.html.twig', [
                'conversation' => $conversation,
                'form' => $form->createView(),
            ]);
    }
}
