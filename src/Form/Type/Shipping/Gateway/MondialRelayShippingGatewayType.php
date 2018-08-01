<?php

namespace MagentixMondialRelayPlugin\Form\Type\Shipping\Gateway;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class MondialRelayShippingGatewayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('api_wsdl', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_wsdl',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL'
            ])
            ->add('api_company', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_company',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'BDTEST13'
            ])
            ->add('api_reference', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_reference',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => '11'
            ])
            ->add('api_key', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_key',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => 'PrivateK'
            ])
            ->add('number', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.number',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => '10'
            ])
        ;
    }
}