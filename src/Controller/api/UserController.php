<?php

namespace App\Controller\api;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * List all users.
     *
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'api_users_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of users, can be empty',
    )]
    #[OA\Response(
        response: 204,
        description: 'User list is empty',
    )]
    #[Security(name: 'Bearer')]
    #[OA\Tag(name: 'users')]
    public function getUsers(): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAll();

        if (!$users) {
            return new JsonResponse(['message' => 'User list is empty'], Response::HTTP_NO_CONTENT);
        }

        $i = 0;
        $data = [];

        foreach ($users as $user) {
            $data[$i]['id']    = $user->getId();
            $data[$i]['name']  = $user->getName();
            $data[$i]['email'] = $user->getEmail();
            $data[$i]['group'] = $user->getGroup()->getName();

            ++$i;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * List all users of a specific group.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/groups/{id}/users', name: 'api_users_of_category_get', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The group ID',
        required: true,
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of users of a specific category, can be empty',
    )]
    #[OA\Response(
        response: 204,
        description: 'User list for a specific group is empty',
    )]
    #[Security(name: 'Bearer')]
    #[OA\Tag(name: 'users')]
    public function getUsersByCategory(int $id): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAllByGroupId($id);

        if (!$users) {
            return new JsonResponse([
                'message' => 'User list for a specific group is empty'],
                Response::HTTP_NO_CONTENT
            );
        }

        $i = 0;
        $data = [];

        foreach ($users as $user) {
            $data[$i]['id']    = $user->getId();
            $data[$i]['name']  = $user->getName();
            $data[$i]['email'] = $user->getEmail();
            $data[$i]['group'] = $user->getGroup()->getName();

            ++$i;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Retrieve a specific user.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'api_users_get_by_id', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The user ID',
        required: true,
    )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Retrieve a specific user',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Tag(name: 'users')]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id'    => $user->getId(),
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
            'group' => $user->getGroup()->getName(),
        ];

        return new JsonResponse($data);
    }

    /**
     * Delete a specific user.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'api_users_delete_by_id', methods: ['DELETE'])]
    #[OA\Parameter(
         name: 'id',
         in: 'path',
         description: 'The user ID',
         required: true,
     )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 204,
        description: 'User deleted',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Tag(name: 'users')]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'User deleted'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a user.
     *
     * TODO: Create a custom Constraint for Groups for User Entity.
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'api_users_create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'User created',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                'name'     => 'Another awesome user name',
                'email'    => 'AnotherAwesomeAser@herdwatch.com',
                'group_id' => '1',
            ]
        )
    )]
    #[OA\Tag(name: 'users')]
    public function createUser(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();

        if (!isset($data['group_id']) || !is_numeric($data['group_id'])) {
            return new JsonResponse(['message' => 'User group is incorrect'], Response::HTTP_BAD_REQUEST);
        }

        $group = $this->em->getRepository(Group::class)->find($data['group_id']);

        if (!$group) {
            return new JsonResponse(
                [
                    'message' => 'User group Id provided is incorrect'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setGroup($group);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            $data = [
                'type'   => 'validation_error',
                'title'  => 'There was a validation error',
                'errors' =>  $errorMessages
            ];

            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    }

    /**
     * Update an existing user.
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'api_users_update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'User updated',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Response(
        response: 400,
        description: 'User group is incorrect',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                'name'     => 'New user awesome name',
                'email'    => 'New_AnotherAwesomeAser@herdwatch.com',
                'group_id' => '2',
            ]
        )
    )]
    #[OA\Tag(name: 'users')]
    public function updateUser(Request $request, ValidatorInterface $validator, int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $user->setName($data['name'] ?? $user->getName());
        $user->setEmail($data['email'] ?? $user->getEmail());

        if (isset($data['group_id']) && is_numeric($data['group_id'])) {
            $group = $this->em->getRepository(Group::class)->find($data['group_id']);

            if (!$group) {
                return new JsonResponse(
                    [
                        'message' => 'User group Id provided is incorrect'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user->setGroup($group);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            $data = [
                'type'   => 'validation_error',
                'title'  => 'There was a validation error',
                'errors' =>  $errorMessages
            ];

            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
    }
}