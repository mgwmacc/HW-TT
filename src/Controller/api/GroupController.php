<?php

namespace App\Controller\api;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class GroupController extends AbstractController
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
     * List all groups.
     */
    #[Route('/api/groups', name: 'api_groups_get', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of user groups',
    )]
    #[OA\Response(
        response: 204,
        description: 'Group list is empty',
    )]
    #[OA\Tag(name: 'groups')]
    public function getGroups(): JsonResponse
    {
        $groups = $this->em->getRepository(Group::class)->findAll();

        if (!$groups) {
            return new JsonResponse(['message' => 'Group list is empty'], Response::HTTP_NO_CONTENT);
        }

        $i = 0;
        $data = [];

        foreach ($groups as $group) {
            $data[$i]['id']    = $group->getId();
            $data[$i]['name']  = $group->getName();

            $i++;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Retrieve a specific group.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/groups/{id}', name: 'api_groups_get_by_id', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Retrieve a specific group',
    )]
    #[OA\Response(
        response: 404,
        description: 'Group is not found',
    )]
    #[OA\Tag(name: 'groups')]
    public function getGroupById(int $id): JsonResponse
    {
        $group = $this->em->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['message' => 'Group is not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id'    => $group->getId(),
            'name'  => $group->getName(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Delete a specific group.
     *
     * Note: all the users of a specific group will be removed as well.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/groups/{id}', name: 'api_group_delete_by_id', methods: ['DELETE'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The group ID',
        required: true,
    )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 204,
        description: 'Group deleted',
    )]
    #[OA\Response(
        response: 404,
        description: 'Group not found',
    )]
    #[OA\Tag(name: 'groups')]
    public function deleteGroup(int $id): JsonResponse
    {
        $user = $this->em->getRepository(Group::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse(['message' => 'Group deleted'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a group.
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/groups', name: 'api_groups_create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Group created',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                'name' => 'Another awesome group'
            ]
        )
    )]
    #[OA\Tag(name: 'groups')]
    public function createGroup(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $group = new Group();
        $group->setName($data['name']);

        $errors = $validator->validate($group);

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

        $this->em->persist($group);
        $this->em->flush();

        return new JsonResponse(['message' => 'Group created'], Response::HTTP_CREATED);
    }

    /**
     * Update an existing group.
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/groups/{id}', name: 'api_groups_update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Group updated',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\Response(
        response: 404,
        description: 'Group not found',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                'name' => 'New group name'
            ]
        )
    )]
    #[OA\Tag(name: 'groups')]
    public function updateGroup(Request $request,  ValidatorInterface $validator, int $id): JsonResponse
    {
        $group = $this->em->getRepository(Group::class)->find($id);

        if (!$group) {
            return new JsonResponse(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $group->setName($data['name'] ?? $group->getName());

        $errors = $validator->validate($group);

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

        $this->em->flush();

        return new JsonResponse(['message' => 'Group updated'], Response::HTTP_OK);
    }
}