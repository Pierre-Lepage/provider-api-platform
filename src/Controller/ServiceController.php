<?php
// Chemin : src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ServiceController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private ProviderRepository $providerRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    // 1. GET /api/services : Récupérer la liste des services
    #[Route('/services', name: 'get_services', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $services = $this->serviceRepository->findAll();
        $jsonServices = $this->serializer->serialize($services, 'json', ['groups' => 'service:read']);

        return new JsonResponse($jsonServices, JsonResponse::HTTP_OK, [], true);
    }

    // 2. POST /api/services : Ajouter un nouveau service
    #[Route('/services', name: 'create_service', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Vérifier si le provider_id est présent
            if (!isset($data['provider_id'])) {
                return new JsonResponse(
                    ['error' => 'Provider ID is required'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            // Récupérer le provider associé
            $provider = $this->providerRepository->find($data['provider_id']);
            if (!$provider) {
                return new JsonResponse(
                    ['error' => 'Provider not found'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            // Créer le service
            $service = new Service();
            $service->setName($data['name'] ?? null);
            $service->setDescription($data['description'] ?? null);
            $service->setPrice($data['price'] ?? null);
            $service->setProvider($provider);

            // Valider l'entité
            $errors = $this->validator->validate($service);
            if (count($errors) > 0) {
                return new JsonResponse(
                    $this->serializer->serialize($errors, 'json'),
                    JsonResponse::HTTP_BAD_REQUEST,
                    [],
                    true
                );
            }

            $this->entityManager->persist($service);
            $this->entityManager->flush();

            $jsonService = $this->serializer->serialize($service, 'json', ['groups' => 'service:read']);

            return new JsonResponse(
                $jsonService,
                JsonResponse::HTTP_CREATED,
                [],
                true
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    // 3. PUT /api/services/{id} : Modifier un service existant
    #[Route('/services/{id}', name: 'update_service', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $service = $this->serviceRepository->find($id);
        if (!$service) {
            return new JsonResponse(
                ['error' => 'Service not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $data = json_decode($request->getContent(), true);

            if (isset($data['name'])) {
                $service->setName($data['name']);
            }
            if (isset($data['description'])) {
                $service->setDescription($data['description']);
            }
            if (isset($data['price'])) {
                $service->setPrice($data['price']);
            }
            if (isset($data['provider_id'])) {
                $provider = $this->providerRepository->find($data['provider_id']);
                if (!$provider) {
                    return new JsonResponse(
                        ['error' => 'Provider not found'],
                        JsonResponse::HTTP_BAD_REQUEST
                    );
                }
                $service->setProvider($provider);
            }

            // Valider l'entité
            $errors = $this->validator->validate($service);
            if (count($errors) > 0) {
                return new JsonResponse(
                    $this->serializer->serialize($errors, 'json'),
                    JsonResponse::HTTP_BAD_REQUEST,
                    [],
                    true
                );
            }

            $this->entityManager->flush();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    // 4. DELETE /api/services/{id} : Supprimer un service
    #[Route('/services/{id}', name: 'delete_service', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $service = $this->serviceRepository->find($id);
        if (!$service) {
            return new JsonResponse(
                ['error' => 'Service not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
