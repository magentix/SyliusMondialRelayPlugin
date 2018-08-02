<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace MagentixMondialRelayPlugin\Form\Type\Shipping\Calculator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MondialRelayConfigurationType extends AbstractType
{

    /**
     * @var AbstractType $rangeType
     */
    protected $rangeType;

    /**
     * WeightRangeConfigurationType constructor.
     *
     * @param AbstractType $rangeType
     */
    public function __construct(AbstractType $rangeType)
    {
        $this->rangeType = $rangeType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ranges', CollectionType::class, [
                'label' => 'mondial_relay.form.shipping_method.amount',
                'entry_type' => get_class($this->rangeType),
                'entry_options' => [
                    'attr' => [
                        'class' => 'three fields',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'ranges' => [],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_shipping_calculator_mondial_relay';
    }
}
