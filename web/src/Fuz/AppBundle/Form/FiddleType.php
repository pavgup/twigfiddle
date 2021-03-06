<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Util\ProcessConfiguration;
use Fuz\AppBundle\Transformer\FiddleTagTransformer;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleType extends AbstractType
{

    protected $twigVersions;

    public function __construct(ProcessConfiguration $processConfiguration)
    {
        $cfg = $processConfiguration->getProcessConfig();

        $this->twigVersions = $cfg['supported_versions'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FiddleTagTransformer($options['data_object']);

        $this->buildTwigVersionChoices($builder, $options);

        $builder
           ->add('templates', 'collection',
              array (
                   'type' => new FiddleTemplateType(),
                   'allow_add' => true,
                   'allow_delete' => true,
                   'prototype' => true,
                   'error_bubbling' => false,
                   'by_reference' => false,
                   'required' => false,
           ))
           ->add('context', new FiddleContextType(), array (
                   'required' => false,
           ))
           ->add('title', 'text', array (
                   'required' => false,
           ))
           ->add(
              $builder
              ->create('tags', 'text', array (
                      'required' => false,
              ))
              ->addModelTransformer($transformer)
           )
           ->add('visibility', 'choice',
              array (
                   'choices' => array (
                           Fiddle::VISIBILITY_PUBLIC => 1,
                           Fiddle::VISIBILITY_UNLISTED => 2,
                           Fiddle::VISIBILITY_PRIVATE => 3,
                   )
           ))
        ;
    }

    public function buildTwigVersionChoices(FormBuilderInterface $builder, array $options)
    {
        $fiddle = $options['data_object'];
        $engine = key($this->twigVersions);
        if (!is_null($fiddle))
        {
            foreach ($this->twigVersions as $twigEngine => $twigVersions)
            {
                if (in_array($fiddle->getTwigVersion(), $twigVersions))
                {
                    $engine = $twigEngine;
                    break ;
                }
            }
        }

        $engines = array_keys($this->twigVersions);
        $choices = call_user_func_array('array_merge', $this->twigVersions);

        $builder
           ->add('twigEngine', 'choice',
              array (
                   'choices' => array_combine($engines, $engines),
                   'required' => true,
                   'mapped' => false,
           ))
           ->add('twigVersion', 'choice',
              array (
                   'choices' => array_combine($choices, $choices),
                   'required' => true,
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\Fiddle',
        ));
    }

    public function getName()
    {
        return 'FiddleType';
    }

}
