<?php

namespace CoreBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FonctionType extends AbstractType
{
    /**
     * @var string
     */
    private $submitName;

    /**
     * FonctionType constructor.
     */
    public function __construct()
    {
        $path = substr(Request::createFromGlobals()->getPathInfo(), 17, 4);

        if ($path == 'add')
        {
            $this->submitName = 'Envoyer';
        }
        if ($path == 'edit')
        {
            $this->submitName = 'Mettre & Jour';
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, array(
                'label' => 'id',
            ))
            ->add('name', TextType::class, array(
                'label' => 'Nom de la fonction',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
            ->add('shortName', TextType::class, array(
                'label' => 'Nom raccourci',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
            ->add('nameInCompany', TextType::class, array(
                'label' => 'Dans Company',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
            ->add('nameInOdigo', TextType::class, array(
                'label' => 'Dans Odigo',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
            ->add('nameInSalesforce', TextType::class, array(
                'label' => 'Dans Salesforce',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
            ->add('nameInZendesk', TextType::class, array(
                'label' => 'Dans Zendesk',
                'label_attr' => array(
                    'class' => 'col-sm-3 control-label align_right font_exo_2',
                ),
                'attr' => array(
                    'class' => 'form-control font_exo_2',
                ),
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Admin\Fonction'
        ));
    }
}