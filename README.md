# Polidog/HypermediaBundle

A Support HypermediaAPI on Symfony Framework.
Supported type only HAL. 
And You mast need [polidog/simple-api-bundle](https://packagist.org/packages/polidog/simple-api-bundle)

## Installation

```shell script
$ composer require polidog/hypermedia-bundle "dev-master" 
```

```php
<?php

return [
...



    Polidog\HypermediaBundle\HypermediaBundle::class => ['all' => true]
];

```

### Introduce bundle configuration to your config file

```yaml
# config/packages/polidog_hypermedia.yml

polidog_hypermedia: ~
    hal_content_type: false # default false
```

If `hal_content_type` is true that need request header for `application/hal+json`.


## Usage

```php
<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\LoginUser;
use Polidog\SimpleApiBundle\Annotations\Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Polidog\HyperMediaBundle\Annotations\Embed;
use Polidog\HyperMediaBundle\Annotations\Link;

class UserController
{
    private $userRepository;

    /**
     * @Route("/user/{id}")
     * @Api()
     * @Link(rel="project", href="/project")
     * @Embed()  
     */
    public function me($id): array
    {
        $user = $this->userRepository->find($id);
        return [
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'avatar' => $user->getAvatar(),
        ];
    }

    /**
     * @Route("/user/post", methods={"POST"})
     * @Api(statusCode=201)
     */
    public function post(Request $request): array
    {
        // TODO save logic.
        return [
            'status' => 'ok',
        ];
    }
}

```
