<?php

/**
 * @author BRAMILLE Sébastien <contact@oktapodia.com>
 */

namespace Maxmind\Bundle\GeoipBundle\Twig;

use Maxmind\Bundle\GeoipBundle\Service\GeoipManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Error\RuntimeError;
use Twig\Environment;

/**
 * Class GeoipExtension.
 */
class GeoipExtension extends AbstractExtension
{
    /**
     * @var GeoipManager
     */
    private $geoipManager;

    /**
     * GeoipExtension constructor.
     *
     * @param GeoipManager $geoipManager
     */
    public function __construct(GeoipManager $geoipManager)
    {
        $this->geoipManager = $geoipManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('geoip', [$this, 'geoipFilter']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'code',
                [$this, 'getCode'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * @param string $ip
     *
     * @return false|GeoipManager
     */
    public function geoipFilter($ip)
    {
        return $this->geoipManager->lookup($ip);
    }

    /**
     * @param Environment $env
     * @param mixed $template
     *
     * @return bool|mixed
     *
     * @throws RuntimeError
     */
    public function getCode(Environment $env, $template)
    {
        if ($env->hasExtension('demo')) {
            $functions = $env->getExtension('demo')->getFunctions();
            foreach ($functions as $function) {
                if ($function->getName() === 'code') {
                    return call_user_func($function->getCallable(), $template);
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'geoip_extension';
    }
}
