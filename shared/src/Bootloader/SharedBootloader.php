<?php

declare(strict_types=1);

namespace Spiral\Shared\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\InterceptableCore;
use Spiral\RoadRunner\GRPC\InvokerInterface;
use Spiral\Shared\Config\GRPCServicesConfig;
use Spiral\Shared\GRPC\Invoker;
use Spiral\Shared\GRPC\InvokerCore;
use Spiral\Shared\GRPC\ServiceClientCore;
use Spiral\Shared\Services\Blog\v1\BlogServiceClient;
use Spiral\Shared\Services\Blog\v1\BlogServiceInterface;
use Spiral\Shared\Services\Users\v1\UserServiceClient;
use Spiral\Shared\Services\Users\v1\UserServiceInterface;

class SharedBootloader extends Bootloader
{
	protected const DEPENDENCIES = [
	    \Ruvents\SpiralJwt\JwtAuthBootloader::class,
	];

	protected const SINGLETONS = [
	    InvokerInterface::class => [self::class, 'initInvoker'],
	];

	public function __construct(private ConfiguratorInterface $config)
	{
	}


	public function boot(EnvironmentInterface $env): void
	{
		$this->initConfig($env);
	}


	private function initInvoker(Container $container): InvokerInterface
	{
		return new Invoker(
		    $container,
		    new InterceptableCore($container->get(InvokerCore::class)),
		);
	}


	public function start(Container $container): void
	{
		$this->initServices($container);
	}


	private function initConfig(EnvironmentInterface $env)
	{
		$this->config->setDefaults(
		    GRPCServicesConfig::CONFIG,
		    [
		        'services' => [
		            UserServiceClient::class => ['host' => $env->get('USERSERVICE_HOST', '127.0.0.1:9000')],
					BlogServiceClient::class => ['host' => $env->get('BLOGSERVICE_HOST', '127.0.0.1:9001')],
		        ],
		    ]
		);
	}


	private function initServices(Container $container): void
	{
		$credentials = \Grpc\ChannelCredentials::createInsecure();

		$container->bindSingleton(
		    UserServiceInterface::class,
		    static fn(GRPCServicesConfig $config): UserServiceInterface =>  new UserServiceClient(
		        new InterceptableCore(new ServiceClientCore(
		            $config->getService(UserServiceClient::class)['host'],
		            ['credentials' => $credentials]
		        ))
		    )
		);

		$container->bindSingleton(
		    BlogServiceInterface::class,
		    static fn(GRPCServicesConfig $config): BlogServiceInterface =>  new BlogServiceClient(
		        new InterceptableCore(new ServiceClientCore(
		            $config->getService(BlogServiceClient::class)['host'],
		            ['credentials' => $credentials]
		        ))
		    )
		);
	}
}
