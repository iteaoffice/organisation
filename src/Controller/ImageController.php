<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Laminas\Http\Response;
use Organisation\Entity\Logo;
use Organisation\Entity\UpdateLogo;
use Organisation\Service\OrganisationService;

/**
 * Class ImageController
 *
 * @package Organisation\Controller
 */
final class ImageController extends OrganisationAbstractController
{
    private OrganisationService $organisationService;

    public function __construct(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;
    }

    public function organisationLogoAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var Logo $logo */
        $logo = $this->organisationService->find(Logo::class, (int) $this->params('id'));


        if (null === $logo) {
            return $response;
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Type: ' . $logo->getContentType()->getContentType());

        $response->setContent(stream_get_contents($logo->getOrganisationLogo()));

        return $response;
    }

    public function organisationUpdateLogoAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var UpdateLogo $logo */
        $logo = $this->organisationService->find(UpdateLogo::class, (int) $this->params('id'));

        if (null === $logo) {
            return $response;
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Type: ' . $logo->getContentType()->getContentType());

        $response->setContent(stream_get_contents($logo->getOrganisationLogo()));

        return $response;
    }
}
