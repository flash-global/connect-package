<?php

namespace ObjectivePHP\Package\Connect\Middleware;

use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Client\Token;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Workflow\Filter\UrlFilter;
use ObjectivePHP\Package\Connect\ConnectPackage;
use ObjectivePHP\Package\Connect\Exception\AccessDeniedException;

/**
 * Class ConnectMiddleware
 *
 * @package ObjectivePHP\Package\Connect
 */
class ConnectMiddleware
{
    /**
     * @var string
     */
    protected $apiMatcher;

    /**
     * @var int
     */
    protected $flags;

    /**
     * ConnectMiddleware constructor.
     * @param string $apiMatcher
     * @param int $flags
     */
    public function __construct(string $apiMatcher = '/api/*', int $flags = 0)
    {
        $this->apiMatcher = $apiMatcher;
        $this->flags = $flags;
    }

    /**
     * Handle request by Connect client
     *
     * @param ApplicationInterface $app
     * @return \Psr\Http\Message\ResponseInterface
     * @throws AccessDeniedException
     */
    public function __invoke(ApplicationInterface $app)
    {
        $filter = new UrlFilter($this->apiMatcher);
        $filter->setApplication($app);
        $filter = $filter->run($app);

        if ($this->flags & ConnectPackage::API_SAFE_MODE && $filter) {
            $request = $app->getRequest();

            /** @var Token $tokenClient */
            $tokenClient = $app->getServicesFactory()->get('connect.client.token');
            $connectToken = $request->getHeader('connect-token');

            if (empty($connectToken)) {
                throw new AccessDeniedException('Access denied!');
            }

            $token = reset($connectToken);
            if (!$tokenClient->validate($token, $application = true)) {
                throw new AccessDeniedException('Access denied!');
            }
        } elseif (!$filter) {
            /** @var Connect $connect */
            $connect = $app->getServicesFactory()->get('connect.client');

            $connect->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

            return $connect->getResponse();
        }
    }
}
