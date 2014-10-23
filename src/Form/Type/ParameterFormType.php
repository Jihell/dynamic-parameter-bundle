<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class ParameterFormType
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterFormType extends AbstractType
{
    /**
     * @see {inherit}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'jihel.plugin.dynamic_parameter.parameter.form.name',
                'attr' => array(
                    'placeholder' => 'jihel.plugin.dynamic_parameter.parameter.form.name.placeholder',
                ),
            ))
            ->add('namespace', null, array(
                'label' => 'jihel.plugin.dynamic_parameter.parameter.form.namespace',
                'attr' => array(
                    'placeholder' => 'jihel.plugin.dynamic_parameter.parameter.form.namespace.placeholder',
                ),
            ))
            ->add('value', null, array(
                'label' => 'jihel.plugin.dynamic_parameter.parameter.form.value',
                'attr' => array(
                    'placeholder' => 'jihel.plugin.dynamic_parameter.parameter.form.value.placeholder',
                ),
            ))
            ->add('type', 'choice', array(
                'label' => 'jihel.plugin.dynamic_parameter.parameter.form.type',
                'choices' => array(
                    'int'       => 'Integer',
                    'float'     => 'Float',
                    'bool'      => 'Boolean',
                    'string'    => 'String',
                    'json'      => 'Json',
                ),
                'empty_value' => 'jihel.plugin.dynamic_parameter.parameter.form.type.empty_value',
            ))
            ->add('submit', 'submit', array(
                'label' => 'jihel.plugin.dynamic_parameter.parameter.form.submit',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
        ));
    }

    /**
     * @see {inherit}
     */
    public function getName()
    {
        return 'jihel_plugin_dynamic_parameter_parameter';
    }
}
