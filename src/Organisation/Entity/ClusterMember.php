<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Organisation
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Organisation\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ClusterMember
 *
 * @ORM\Table(name="cluster_organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("cluster_organisation")
 */
class ClusterMember // extends EntityAbstract
{
    /**
     * @ORM\Column(name="cluster_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="clusterMember", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Cluster", inversedBy="clusterMember", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="cluster_id")
     * })
     * @var \Organisation\Entity\Cluster
     */
    private $cluster;
}
