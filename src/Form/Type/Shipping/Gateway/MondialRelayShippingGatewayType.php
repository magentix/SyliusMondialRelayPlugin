<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusMondialRelayPlugin\Form\Type\Shipping\Gateway;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContext;

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
                    new Constraints\Url([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL',
                'attr' => [
                    'class' => 'form-section'
                ]
            ])
            ->add('api_company', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_company',
                'constraints' => [
                    new Constraints\NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'BDTEST13'
            ])
            ->add('api_reference', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_reference',
                'constraints' => [
                    new Constraints\NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => '11'
            ])
            ->add('api_key', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.api_key',
                'constraints' => [
                    new Constraints\NotBlank([
                        'groups' => 'bitbag',
                    ]),
                ],
                'empty_data' => 'PrivateK',
            ])
            ->add('pickup_number', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.pickup_number',
                'constraints' => [
                    new Constraints\Range([
                        'groups' => 'bitbag',
                        'min'    => 1,
                        'max'    => 60
                    ]),
                ],
                'empty_data' => '10',
                'attr' => [
                    'class' => 'form-section'
                ]
            ])
            ->add('product_weight', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.product_weight',
                'constraints' => [
                    new Constraints\Choice([
                        'groups'  => 'bitbag',
                        'choices' => [1, 1000],
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
                    new Constraints\Choice([
                        'groups' => 'bitbag',
                        'choices' => [0, 1],
                    ]),
                ],
                'choices' => [
                    'mondial_relay.form.shipping_gateway.no'  => 0,
                    'mondial_relay.form.shipping_gateway.yes' => 1,
                ],
                'attr' => [
                    'class' => 'form-section'
                ]
            ])
            ->add('label_size', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_size',
                'constraints' => [
                    new Constraints\Choice([
                        'groups' => 'bitbag',
                        'choices' => ['URL_PDF_10x15', 'URL_PDF_A4', 'URL_PDF_A5'],
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
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
            ->add('label_shipper_street', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_street',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
            ->add('label_shipper_city', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_city',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
            ->add('label_shipper_postcode', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_postcode',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
            ->add('label_shipper_country_code', ChoiceType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_country_code',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelCountryValidation']
                    ]),
                ],
                'choices' => array_flip(Intl::getRegionBundle()->getCountryNames()),
            ])
            ->add('label_shipper_phone_number', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_phone_number',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
            ->add('label_shipper_email', TextType::class, [
                'label' => 'mondial_relay.form.shipping_gateway.label_shipper_email',
                'constraints' => [
                    new Constraints\Callback([
                        'groups'   => 'bitbag',
                        'callback' => [$this, 'labelNotBlankValidation']
                    ]),
                ],
            ])
        ;
    }

    /**
     * Label fields validation
     *
     * @param string $value
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function labelNotBlankValidation($value, ExecutionContext $context)
    {
        /** @var \Symfony\Component\Form\Form $object */
        $object = $context->getObject();

        $label = (int)$object->getParent()->get('label_generate')->getData();

        if ($label === 1) {
            $constraint = new Constraints\NotBlank(['groups' => 'bitbag']);
            $validator  = new Constraints\NotBlankValidator();
            $validator->initialize($context);

            $validator->validate($value, $constraint);
        }
    }

    /**
     * Label fields validation
     *
     * @param string $value
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function labelCountryValidation($value, ExecutionContext $context)
    {
        /** @var \Symfony\Component\Form\Form $object */
        $object = $context->getObject();

        $label = (int)$object->getParent()->get('label_generate')->getData();

        if ($label === 1) {
            $constraint = new Constraints\Country(['groups' => 'bitbag']);
            $validator  = new Constraints\CountryValidator();
            $validator->initialize($context);

            $validator->validate($value, $constraint);
        }
    }
}
