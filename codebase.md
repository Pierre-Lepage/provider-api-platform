# .aidigestignore

```
# Ignorer les dossiers de dépendances
/vendor/
/node_modules/

# Ignorer les dossiers de cache
/var/cache/
/var/logs/
/var/sessions/
/public/bundles/

# Ignorer les fichiers de configuration sensibles
.env
.env.local
.env.*.local

# Ignorer les fichiers de déploiement et build
/.git
/.github
/.idea
/.vscode

# Ignorer les fichiers spécifiques à l'environnement local
*.log
*.lock
.DS_Store
thumbs.db

```

# .gitignore

```

###> symfony/framework-bundle ###
/.env.local
/.env.local.php
/.env.*.local
/config/secrets/prod/prod.decrypt.private.php
/public/bundles/
/var/
/vendor/
###< symfony/framework-bundle ###

```

# compose.override.yaml

```yaml

services:
###> doctrine/doctrine-bundle ###
  database:
    ports:
      - "5432"
###< doctrine/doctrine-bundle ###

```

# compose.yaml

```yaml

services:
###> doctrine/doctrine-bundle ###
  database:
    image: postgres:${POSTGRES_VERSION:-16}-alpine
    environment:
      POSTGRES_DB: ${POSTGRES_DB:-app}
      # You should definitely change the password in production
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-!ChangeMe!}
      POSTGRES_USER: ${POSTGRES_USER:-app}
    volumes:
      - database_data:/var/lib/postgresql/data:rw
      # You may use a bind-mounted host directory instead, so that it is harder to accidentally remove the volume and lose all your data!
      # - ./docker/db/data:/var/lib/postgresql/data:rw
###< doctrine/doctrine-bundle ###

volumes:
###> doctrine/doctrine-bundle ###
  database_data:
###< doctrine/doctrine-bundle ###

```

# composer.json

```json
{
    "type": "project",
    "license": "proprietary",
    "minimum-stability": "stable",
    "prefer-stable": true,
    "require": {
        "php": ">=8.1",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "doctrine/dbal": "^3",
        "doctrine/doctrine-bundle": "^2.13",
        "doctrine/doctrine-migrations-bundle": "^3.3",
        "doctrine/orm": "^3.3",
        "phpdocumentor/reflection-docblock": "^5.4",
        "phpstan/phpdoc-parser": "^1.33",
        "symfony/console": "6.4.*",
        "symfony/dotenv": "6.4.*",
        "symfony/flex": "^2",
        "symfony/framework-bundle": "6.4.*",
        "symfony/property-access": "6.4.*",
        "symfony/property-info": "6.4.*",
        "symfony/runtime": "6.4.*",
        "symfony/security-bundle": "6.4.*",
        "symfony/serializer": "6.4.*",
        "symfony/validator": "6.4.*",
        "symfony/yaml": "6.4.*"
    },
    "config": {
        "allow-plugins": {
            "php-http/discovery": true,
            "symfony/flex": true,
            "symfony/runtime": true
        },
        "sort-packages": true
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\Tests\\": "tests/"
        }
    },
    "replace": {
        "symfony/polyfill-ctype": "*",
        "symfony/polyfill-iconv": "*",
        "symfony/polyfill-php72": "*",
        "symfony/polyfill-php73": "*",
        "symfony/polyfill-php74": "*",
        "symfony/polyfill-php80": "*",
        "symfony/polyfill-php81": "*"
    },
    "scripts": {
        "auto-scripts": {
            "cache:clear": "symfony-cmd",
            "assets:install %PUBLIC_DIR%": "symfony-cmd"
        },
        "post-install-cmd": [
            "@auto-scripts"
        ],
        "post-update-cmd": [
            "@auto-scripts"
        ]
    },
    "conflict": {
        "symfony/symfony": "*"
    },
    "extra": {
        "symfony": {
            "allow-contrib": false,
            "require": "6.4.*"
        }
    },
    "require-dev": {
        "symfony/maker-bundle": "^1.61"
    }
}

```

# config/bundles.php

```php
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
];

```

# config/packages/cache.yaml

```yaml
framework:
    cache:
        # Unique name of your app: used to compute stable namespaces for cache keys.
        #prefix_seed: your_vendor_name/app_name

        # The "app" cache stores to the filesystem by default.
        # The data in this cache should persist between deploys.
        # Other options include:

        # Redis
        #app: cache.adapter.redis
        #default_redis_provider: redis://localhost

        # APCu (not recommended with heavy random-write workloads as memory fragmentation can cause perf issues)
        #app: cache.adapter.apcu

        # Namespaced pools use the above "app" backend by default
        #pools:
            #my.dedicated.cache: null

```

# config/packages/doctrine_migrations.yaml

```yaml
doctrine_migrations:
    migrations_paths:
        # namespace is arbitrary but should be different from App\Migrations
        # as migrations classes should NOT be autoloaded
        'DoctrineMigrations': '%kernel.project_dir%/migrations'
    enable_profiler: false

```

# config/packages/doctrine.yaml

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'

        # IMPORTANT: You MUST configure your server version,
        # either here or in the DATABASE_URL env var (see .env file)
        #server_version: '16'

        profiling_collect_backtrace: '%kernel.debug%'
        use_savepoints: true
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        report_fields_where_declared: true
        validate_xml_mapping: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App

when@test:
    doctrine:
        dbal:
            # "TEST_TOKEN" is typically set by ParaTest
            dbname_suffix: '_test%env(default::TEST_TOKEN)%'

when@prod:
    doctrine:
        orm:
            auto_generate_proxy_classes: false
            proxy_dir: '%kernel.build_dir%/doctrine/orm/Proxies'
            query_cache_driver:
                type: pool
                pool: doctrine.system_cache_pool
            result_cache_driver:
                type: pool
                pool: doctrine.result_cache_pool

    framework:
        cache:
            pools:
                doctrine.result_cache_pool:
                    adapter: cache.app
                doctrine.system_cache_pool:
                    adapter: cache.system

```

# config/packages/framework.yaml

```yaml
# see https://symfony.com/doc/current/reference/configuration/framework.html
framework:
    secret: '%env(APP_SECRET)%'
    annotations: false
    http_method_override: false
    handle_all_throwables: true

    # Enables session support. Note that the session will ONLY be started if you read or write from it.
    # Remove or comment this section to explicitly disable session support.
    session:
        handler_id: null
        cookie_secure: auto
        cookie_samesite: lax

    #esi: true
    #fragments: true
    php_errors:
        log: true

when@test:
    framework:
        test: true
        session:
            storage_factory_id: session.storage.factory.mock_file

```

# config/packages/routing.yaml

```yaml
framework:
    router:
        utf8: true

        # Configure how to generate URLs in non-HTTP contexts, such as CLI commands.
        # See https://symfony.com/doc/current/routing.html#generating-urls-in-commands
        #default_uri: http://localhost

when@prod:
    framework:
        router:
            strict_requirements: null

```

# config/packages/security.yaml

```yaml
security:
    # https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    # https://symfony.com/doc/current/security.html#loading-the-user-the-user-provider
    providers:
        users_in_memory: { memory: null }
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: users_in_memory

            # activate different ways to authenticate
            # https://symfony.com/doc/current/security.html#the-firewall

            # https://symfony.com/doc/current/security/impersonating_user.html
            # switch_user: true

    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        # - { path: ^/admin, roles: ROLE_ADMIN }
        # - { path: ^/profile, roles: ROLE_USER }

when@test:
    security:
        password_hashers:
            # By default, password hashers are resource intensive and take time. This is
            # important to generate secure password hashes. In tests however, secure hashes
            # are not important, waste resources and increase test times. The following
            # reduces the work factor to the lowest possible values.
            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
                algorithm: auto
                cost: 4 # Lowest possible value for bcrypt
                time_cost: 3 # Lowest possible value for argon
                memory_cost: 10 # Lowest possible value for argon

```

# config/packages/validator.yaml

```yaml
framework:
    validation:
        email_validation_mode: html5

        # Enables validator auto-mapping support.
        # For instance, basic validation constraints will be inferred from Doctrine's metadata.
        #auto_mapping:
        #    App\Entity\: []

when@test:
    framework:
        validation:
            not_compromised_password: false

```

# config/preload.php

```php
<?php

if (file_exists(dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php';
}

```

# config/routes.yaml

```yaml
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

```

# config/routes/framework.yaml

```yaml
when@dev:
    _errors:
        resource: '@FrameworkBundle/Resources/config/routing/errors.xml'
        prefix: /_error

```

# config/routes/security.yaml

```yaml
_security_logout:
    resource: security.route_loader.logout
    type: service

```

# config/services.yaml

```yaml
# This file is the entry point to configure your own services.
# Files in the packages/ subdirectory configure your dependencies.

# Put parameters here that don't need to change on each machine where the app is deployed
# https://symfony.com/doc/current/best_practices.html#use-parameters-for-application-configuration
parameters:

services:
    # default configuration for services in *this* file
    _defaults:
        autowire: true      # Automatically injects dependencies in your services.
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.

    # makes classes in src/ available to be used as services
    # this creates a service per class whose id is the fully-qualified class name
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # add more service definitions when explicit configuration is needed
    # please note that last definitions always *replace* previous ones

```

# migrations/.gitignore

```

```

# migrations/Version20241025123121.php

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025123121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, provider_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E19D9AD2A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2A53A8AA');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE service');
    }
}

```

# provider-api

This is a binary file of the type: Binary

# public/.htaccess

```
<IfModule mod_rewrite.c>
    Options -MultiViews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

```

# public/index.php

```php
<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

```

# src/Controller/.gitignore

```

```

# src/Controller/ProviderController.php

```php
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

```

# src/Controller/ServiceController.php

```php
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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
            $service = $this->serializer->deserialize(
                $request->getContent(),
                Service::class,
                'json',
                [AbstractNormalizer::GROUPS => ['service:write']]
            );

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

            // Vérifier que le provider existe
            $provider = $service->getProvider();
            if (!$provider || !$provider->getId()) {
                return new JsonResponse(
                    ['error' => 'Provider is required and must exist'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $existingProvider = $this->providerRepository->find($provider->getId());
            if (!$existingProvider) {
                return new JsonResponse(
                    ['error' => 'Provider not found'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $service->setProvider($existingProvider);

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
            $updatedService = $this->serializer->deserialize(
                $request->getContent(),
                Service::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $service,
                    AbstractNormalizer::GROUPS => ['service:write']
                ]
            );

            // Valider l'entité
            $errors = $this->validator->validate($updatedService);
            if (count($errors) > 0) {
                return new JsonResponse(
                    $this->serializer->serialize($errors, 'json'),
                    JsonResponse::HTTP_BAD_REQUEST,
                    [],
                    true
                );
            }

            // Vérifier que le provider existe si modifié
            if ($updatedService->getProvider()) {
                $provider = $this->providerRepository->find($updatedService->getProvider()->getId());
                if (!$provider) {
                    return new JsonResponse(
                        ['error' => 'Provider not found'],
                        JsonResponse::HTTP_BAD_REQUEST
                    );
                }
                $updatedService->setProvider($provider);
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

```

# src/Entity/.gitignore

```

```

# src/Entity/Provider.php

```php
<?php
// Chemin : src/Entity/Provider.php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['provider:read', 'service:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['provider:read', 'provider:write', 'service:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Service::class, orphanRemoval: true)]
    #[Groups(['provider:read'])]
    private Collection $services;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    // **Méthode "getter" pour "name"**
    public function getName(): ?string
    {
        return $this->name;
    }

    // **Méthode "setter" pour "name"**
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // **Méthode "getter" pour "email"**
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // **Méthode "setter" pour "email"**
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // **Méthode "getter" pour "phone"**
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    // **Méthode "setter" pour "phone"**
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    // Méthodes pour "services"

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setProvider($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getProvider() === $this) {
                $service->setProvider(null);
            }
        }

        return $this;
    }

    // Méthodes pour "createdAt" et "updatedAt"

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

```

# src/Entity/Service.php

```php
<?php
// Chemin : src/Entity/Service.php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['service:read', 'service:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['service:read', 'service:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Groups(['service:read', 'service:write'])]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['service:read', 'service:write'])]
    private ?Provider $provider = null;

    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode "getter" pour "name"
    public function getName(): ?string
    {
        return $this->name;
    }

    // Méthode "setter" pour "name"
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Méthode "getter" pour "description"
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Méthode "setter" pour "description"
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Méthode "getter" pour "price"
    public function getPrice(): ?float
    {
        return $this->price;
    }

    // Méthode "setter" pour "price"
    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    // Méthode "getter" pour "provider"
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    // Méthode "setter" pour "provider"
    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    // Méthodes pour "createdAt" et "updatedAt"

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

```

# src/Kernel.php

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}

```

# src/Repository/.gitignore

```

```

# src/Repository/ProviderRepository.php

```php
<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Provider>
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

//    /**
//     * @return Provider[] Returns an array of Provider objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Provider
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

```

# src/Repository/ServiceRepository.php

```php
<?php
// Chemin : src/Repository/ServiceRepository.php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    // Ajoutez des méthodes personnalisées si nécessaire
}


```

