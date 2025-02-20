<?php

namespace App\Controller;

use App\Document\User;
use App\Document\Habit;
use App\Document\Group;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    // ✅ 1. Ajouter une habitude à un utilisateur
    #[Route('/user/{userId}/add-habit', name: 'add_habit', methods: ['POST'])]
    public function addHabit(Request $request, string $userId): Response
    {
        $user = $this->dm->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $habit = new Habit();
        $habit->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'))
              ->setDifficulty($request->request->get('difficulty'))
              ->setPeriodicity($request->request->get('periodicity'));

        $this->dm->persist($habit);
        $user->addHabit($habit);
        $this->dm->flush();

        $this->addFlash('success', 'Habitude ajoutée avec succès.');
        return $this->redirectToRoute('user_profile', ['id' => $userId]);
    }

    // ✅ 2. Associer un utilisateur à un groupe
    #[Route('/user/{userId}/join-group/{groupId}', name: 'join_group', methods: ['GET'])]
    public function joinGroup(string $userId, string $groupId): Response
    {
        $user = $this->dm->getRepository(User::class)->find($userId);
        $group = $this->dm->getRepository(Group::class)->find($groupId);

        if (!$user || !$group) {
            throw $this->createNotFoundException('Utilisateur ou groupe non trouvé.');
        }

        $user->setGroupId($group->getId());
        $this->dm->flush();

        $this->addFlash('success', 'Vous avez rejoint le groupe avec succès.');
        return $this->redirectToRoute('user_profile', ['id' => $userId]);
    }

    // ✅ 3. Afficher le profil d’un utilisateur et ses habitudes
    #[Route('/user/{id}', name: 'user_profile', methods: ['GET'])]
    public function userProfile(string $id): Response
    {
        $user = $this->dm->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'habits' => $user->getHabits(),
        ]);
    }
}
