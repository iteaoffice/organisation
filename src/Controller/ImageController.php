<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Organisation\Entity\Logo;
use Zend\Http\Response;

/**
 * The index of the system.
 *
 * @category Content
 */
class ImageController extends OrganisationAbstractController
{
    /**
     * @return Response
     */
    public function organisationLogoAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $id = $this->params('id');
        if (is_null($id)) {
            return $response;
        }
        /** @var Logo $logo */
        $logo = $this->getOrganisationService()->findEntityById(Logo::class, $id);

        if (is_null($logo)) {
            return $response;
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Type: ' . $logo->getContentType()->getContentType());

        $response->setContent(stream_get_contents($logo->getOrganisationLogo()));

        return $response;
    }
}
