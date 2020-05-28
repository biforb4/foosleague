<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Slack\ParamConverter;

use FoosLeague\Infrastructure\Slack\ACL\SlackCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class SlackCommandConverter implements ParamConverterInterface
{
    private DenormalizerInterface $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $data = array_merge($request->query->all(), $request->attributes->all(), $request->request->all());
        /** @var SlackCommand $dto */
        $dto = $this->denormalizer->denormalize($data, $configuration->getClass());
        $request->attributes->set($configuration->getName(), $dto);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === SlackCommand::class;
    }
}
