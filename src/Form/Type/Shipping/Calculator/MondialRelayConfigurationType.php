<?php

declare(strict_types=1);

namespace MagentixMondialRelayPlugin\Form\Type\Shipping\Calculator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MondialRelayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'mondial.relay.form.shipping_method.amount',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'integer']),
                ],
                'empty_data' => '0'
            ])
            ->add('api_wsdl', TextType::class, [
                'label' => 'mondial.relay.form.shipping_method.api_company',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL'
            ])
            ->add('api_company', TextType::class, [
                'label' => 'mondial.relay.form.shipping_method.api_company',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'BDTEST13'
            ])
            ->add('api_reference', TextType::class, [
                'label' => 'mondial.relay.form.shipping_method.api_reference',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => '11'
            ])
            ->add('api_key', TextType::class, [
                'label' => 'mondial.relay.form.shipping_method.api_key',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'PrivateK'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_shipping_calculator_mondial_relay';
    }
}
