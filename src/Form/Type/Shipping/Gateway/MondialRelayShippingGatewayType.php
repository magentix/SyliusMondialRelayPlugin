<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace MagentixMondialRelayPlugin\Form\Type\Shipping\Gateway;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('mondial_relay', HiddenType::class)
            ->add('api_wsdl', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_wsdl',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL'
            ])
            ->add('api_company', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_company',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'BDTEST13'
            ])
            ->add('api_reference', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_reference',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => '11'
            ])
            ->add('api_key', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_key',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'PrivateK'
            ])
            ->add('pickup_number', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.pickup_number',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => '10'
            ])
            ->add('product_weight', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.product_weight',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'choices' => [
                    'mondial_relay.form.shipping_gateway.kilogram' => 1,
                    'mondial_relay.form.shipping_gateway.gram'     => 1000,
                ],
            ])
            ->add('label_generate', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_generate',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'choices' => [
                    'mondial_relay.form.shipping_gateway.no'  => 0,
                    'mondial_relay.form.shipping_gateway.yes' => 1,
                ],
            ])
            ->add('label_size', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_size',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'choices' => [
                    '10x15' => 'URL_PDF_10x15',
                    'A4'    => 'URL_PDF_A4',
                    'A5'    => 'URL_PDF_A5',
                ],
            ])
            ->add('label_shipper_company', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_company',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_street', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_street',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_city', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_city',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_postcode', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_postcode',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_country_code', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_country_code',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_phone_number', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_phone_number',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('label_shipper_email', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_email',
                'constraints' => [
                    new NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
        ;
    }
}
