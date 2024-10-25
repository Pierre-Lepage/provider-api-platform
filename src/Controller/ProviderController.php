<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ProviderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ProviderRepository $providerRepository
    ) {
    }

    #[Route('/providers', name: 'get_providers', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $providers = $this->providerRepository->findAll();
        $jsonProviders = $this->serializer->serialize($providers, 'json', ['groups' => 'provider:read']);

        return new JsonResponse($jsonProviders, Response::HTTP_OK, [], true);
    }

    #[Route('/providers', name: 'create_provider', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $provider = $this->serializer->deserialize(
                $request->getContent(),
                Provider::class,
                'json',
                [AbstractNormalizer::GROUPS => ['provider:write']]
            );

            $errors = $this->validator->validate($provider);
            if (count($errors) > 0) {
                return new JsonResponse(
                    $this->serializer->serialize($errors, 'json'),
                    Response::HTTP_BAD_REQUEST,
                    [],
                    true
                );
            }

            $this->entityManager->persist($provider);
            $this->entityManager->flush();

            return new JsonResponse(
                $this->serializer->serialize($provider, 'json', ['groups' => 'provider:read']),
                Response::HTTP_CREATED,
                [],
                true
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/providers/{id}', name: 'update_provider', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $provider = $this->providerRepository->find($id);
        if (!$provider) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        try {
            $updatedProvider = $this->serializer->deserialize(
                $request->getContent(),
                Provider::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $provider,
                    AbstractNormalizer::GROUPS => ['provider:write']
                ]
            );

            $errors = $this->validator->validate($updatedProvider);
            if (count($errors) > 0) {
                return new JsonResponse(
                    $this->serializer->serialize($errors, 'json'),
                    Response::HTTP_BAD_REQUEST,
                    [],
                    true
                );
            }

            $this->entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/providers/{id}', name: 'delete_provider', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $provider = $this->providerRepository->find($id);
        if (!$provider) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($provider);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
