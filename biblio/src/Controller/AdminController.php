<?php

namespace App\Controller;

// Import necessary classes and components from the Symfony framework and application
use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Define a route prefix for all routes in this controller
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    // Declare a private property to hold the EntityManagerInterface instance
    private $entityManager;

    // Constructor to inject the EntityManagerInterface dependency
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Define the route for the admin index page
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Render the admin index template and pass the controller name to the template
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    * @Route("/abonnes/list", name="admin_list_abonne", methods={"GET"})
    * 
    * This route handles displaying a list of users.
    * 
    */
    public function listAbonne(UserRepository $users)
    {
        // Render the template for listing users and pass all user entities to it
        return $this->render('admin/abonnes.html.twig', [
            'users' => $users->findAll(),
        ]);
    }

    /**
    * @Route("/abonnes/edit/{id}", name="admin_edit_abonne", methods={"GET","POST"})
    * 
    * This route handles the editing of a specific user identified by {id}.
    * 
    */
    public function editUser(User $user, Request $request, $id) 
    {
        // Fetch the user entity from the database using the provided id
        $user = $this->entityManager->getRepository(User::class)->find($id);

        // Throw a 404 error if the user is not found
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create the form for editing the user and handle the request
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted and valid, update the user entity and redirect to the user list
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('admin_list_abonne');
        }

        // Render the template for editing a user and pass the form view to it
        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
